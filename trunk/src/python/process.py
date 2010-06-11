#!/usr/bin/python

import os, getopt, sys, subprocess, time, re

def alert():
	
	#time, src ip, dst ip, signature trigger
	#alert to database
	
	return

def postProcess(file, rulesFile):
	
	#call the post extraction program
	#subprocess.Popen( [program] + outputFile , shell=True)
	
	#f2 = open(rulesFile, 'r')
	#rules = f2.readlines()
	#f2.close()
	
	#read in rules file, check against extracted text
	
	return

def monitorCapture(outputFolder, rulesFile):
	
	outputFiles = list() #running list of the outputfiles in the directory
	
	#continuously monitor the extracted file folder
	while(true):
		
		outputFilesCurrent = list()
		
		dirList=os.listdir(path) #list all the files in the folder
		for fname in dirList:
			outputFilesCurrent(fname)
		
		if len(outputFilesCurrent) != len(outputFiles):
			# a new file has been added, process it
			# postProcess(file, rulesFile) #...how to add multiple new files?
			# outputFiles.append(file)
			blah
		
		time.sleep(1) #sleep for 1 second


def startCapture(configFile, interface, outputFolder):
	#subprocess.Popen("tcpxtract -d " + interface + " -c " + configFile + " -o " + outputFolder, shell=True)
	return
	
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
	interface = "eth1"
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

	startCapture(configFile, interface, outputFolder)	
	monitorCapture(outputFolder, rulesFile)

if __name__ == "__main__":
    main()
