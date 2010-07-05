#!/usr/bin/python

import os, getopt, sys, subprocess, time, re, shutil, os, socket, struct, threading, pexpect

def dottedQuadToNum(ip):
	"""convert decimal dotted quad string to long integer"""
	return struct.unpack('L',socket.inet_aton(ip))[0]

def numToDottedQuad(n):
	"""convert long int to dotted quad string"""
	return socket.inet_ntoa(struct.pack('L',n))

def alert(alertInfo, ruleInfo):
	"""
	Output a snort alert in the unified2 alert format.
	"""
	(fileName, fromInfo, toInfo) = alertInfo #unpack our alert information
	(msg, sid) = ruleInfo

	# snort unified structure - http://searchsecuritychannel.techtarget.com/tip/0,289483,sid97_gci1339679,00.html
	# and http://www.subukan.com/index.php?fmk=articles.snort_unified

	recordType = 7 # = 'alert' record
	length = 52 # standard alert size here	

	# All of the following is the information present in the snort unified2 format
	sensID = 0 # sensor ID
	eId = 0 # event ID ... how are we going to get this?
	eSec = time.time() # event second
	eMS = 0 # event microsecond - no point in getting this granularity
	sigID = sid # signature ID
	genID = 0 # generator ID
	sigRev = 0 # signature revision - leave 0
	classID = 0 #classification ID ... data-loss?
	priorityID = 0 # priority ID
	ipSRC = dottedQuadToNum(fromInfo.split(":")[0]) # ip source converted to decimal
	ipDST = dottedQuadToNum(toInfo.split(":")[0]) # ip destination converted to decimal
	sPort = int(fromInfo.split(":")[1]) # source port
	dPort = int(toInfo.split(":")[1]) # destination port
	prot = 0 # protocol ... always make this TCP?
	packetAction = 0 # packet action... leave this?
	
	#pack all the alert data into the snort unified2 format
	snortAlert = pack('>IIIIIIIIIIIIIHHBB', recordType, length, sensID, eId, eSec, eMS, sigID, genID, sigRev, classID, priorityID, ipSRC, ipDST, sPort, dPort, prot, packetAction)

	# f = open("/var/log/snort_log", "wb") #open the snort log in binary write mode
	# TODO: how to determine the latest snort log?
	# f.write(snortAlert)
	# f.close()
		
	print "Alert from ",str(ipSRC)

def postProcess(alertInfo, rulesFile):
	"""
	Perform post processing on a file extracted by tcpxtract.
	If a rule matches, trigger alert() to output the appropriate snort alert.

	Meta-data (general)
	http://www.linuxjournal.com/article/7552
	http://www.forensicswiki.org/wiki/Tools:Document_Metadata_Extraction
	http://chicago-ediscovery.com/computer-forensic-howtos/howto-extract-metadata-microsoft-word-linux.html

	Office 2007 metadata -> http://blogs.sans.org/computer-forensics/2009/07/10/office-2007-metadata/

	Office 2003 metadata -> http://windowsir.blogspot.com/2006/09/metadata-and-ediscovery.html

	PDF -> http://www.foolabs.com/xpdf/ (pdfinfo tool in the package)
	"""

	text = ""
	(fileName, fromInfo, toInfo) = alertInfo #unpack the alert information
	extension = fileName.split(".")[-1] #grab the extension of the file to trigger on
	
	if extension in ("docx", "xlsx", "pptx"):
		try : 
			process = subprocess.Popen( "perl cat_open_xml.pl " + fileName , shell=True, stdout=subprocess.PIPE)		
			text = str(process.communicate()[0])
		except : 
			pass
	if extension in ("pdf"):
		try : 
			process = subprocess.Popen( "pdftotext " + fileName + " -", shell=True, stdout=subprocess.PIPE)		
			text = str(process.communicate()[0])
			print "pdftext = " + text
		except : 
			pass
	if extension in ("zip"):
		try : 
			print "zip"
		except : 
			pass
	
	if text != "": #if the post-processing text extraction succeeded...
		f2 = open(rulesFile, 'r')
		rules = f2.readlines()
		f2.close()
	
		#check each of our rules against the extracted document text
		for rule in rules:
			if rule[0] != '#': #skip comments
				xRegex = rule.find("pcre:\"") + 7 #gets the rule begin index value
				yRegex = rule.find("/i\"", xRegex) #gets the rule end index value
				regexStr = rule[xRegex:yRegex]
				regex = re.compile(regexStr, re.IGNORECASE)
				result = regex.search(text)
				
				if result != None:
				
					xMsg = rule.find("msg:\"") + 5
					yMsg = rule.find("\";", xMsg)
					msg = rule[xMsg:yMsg]

					xSid = rule.find("sid:") + 4
					ySid = rule.find(";", xSid)
					sid = rule[xSid:ySid]
				
					ruleInfo = (msg, sid)
					alert(alertInfo, ruleInfo)
				
				#no match

def startCapture(configFile, interface, outputFolder, rulesFile):
	"""
	First kick off the tcpxtract instance with appropriate arguments.
	Then monitor the standard output of tcpxtract for any newly extracted files.
	If a file is extracted, kick off post processing and detection with postProcess()
	"""
	processedFolder = outputFolder + "/processed"
	if not os.path.exists(processedFolder):
		os.mkdir(processedFolder) #if the processed folder doesn't exist, create it
	activeAlerts = 0
	
	command = "tcpxtract -d " + interface + " -c " + configFile + " -o " + outputFolder
	proc = pexpect.spawn(command, timeout=None) #kick off tcpxtract, "timeout=None" prevents early termination

	while True: #continuously monitor the standard output tcpxtract for new alerts
		try:
			line = proc.readline()
			pieces = line.split()
			fileName = pieces[-1].split("/")[-1] # extract the relevant information from the tcpxtract alert record
			fromInfo = pieces[7][1:] #(IP:PORT)
			toInfo = pieces[9][:-2] #(IP:PORT)
			
			shutil.move(outputFolder + "/" + fileName, processedFolder + "/" + fileName) #move our file into the processing folder
			alertInfo = (processedFolder + "/" + fileName, fromInfo, toInfo) # wrap all the alert information up in a tuple					
			postProcess(alertInfo, rulesFile)

		except KeyboardInterrupt:
			sys.exit("\nexiting... see ya!")
	
def usage():
	print "Usage: ./dlpTest.py [OPTIONS] [[-r <RULESFILE>] [-c <CONFIG FILE>]]"
	print "Valid options include:"
	print "  --interface, -i <INTERFACE>\tinterface to capture from (i.e. eth1)"
	print "  --rules, -r <RULESFILE>\tsnort detection rules file"
	print "  --config, -c <CONFIGFILE>\ttcpxtract config file with headers to extract"
	print "  --output, -o <FOLDER>\t\tfolder to save the extracted files to"
	print "  --help, h\t\t\tdisplay this screen"

def main():
	try:
		opts, args = getopt.getopt(sys.argv[1:], "i:r:c:o:h")
	except getopt.GetoptError, err:
		print str(err) # will print something like "option -a not recognized"
		usage()
		sys.exit(2)
	rulesFile = ""
	outputFolder = "./"
	processedFolder = "./"
	interface = "eth0"
	configFile = "tcpxtract.conf"
	
	#grab all of our arguments
	for o, a in opts:
		if o in ("-i", "--interface"):
			interface = a
		elif o in ("-r", "--rules"):
			rulesFile = a
		elif o in ("-c", "--config"):
			configFile = a
		elif o in ("-o", "--output"):
			outputFolder = a
		elif o in ("-h", "--help"):
			usage()
			sys.exit()
		else:
			assert False, "unhandled option"

	if (rulesFile == ""):
		#require these specific arguments
		usage()
		sys.exit()

	startCapture(configFile, interface, outputFolder, rulesFile) # kick off the extraction and monitoring

if __name__ == "__main__":
    main()
