#!/usr/bin/python

import os, getopt, sys, subprocess, time, re

def alert():
	
	#time, src ip, dst ip, signature trigger
	#alert to database
	
	return

def postProcess(outputFile):
	
	#call the post extraction program
	#subprocess.Popen( [program] + outputFile , shell=True)
	
	#crawl result folder, do for rule in rules
	
	return

def monitorCapture(outputFile, seconds, rulesFile):

	time.sleep(seconds)

	f = open(outputFile, 'r')
	captureData = f.read()
	f.close()
	
	# if [pdf signature] in captureData or [office signature] in captureData:
	#	postProcess(outputFile)
		

	f2 = open(rulesFile, 'r')
	rules = f2.readlines()
	f2.close()
	for rule in rules:
		matches = re.findall(pattern, captureData)
		if len(matches) > 0:
			print "match found"
			#alert()


def startCapture(homeNetwork, seconds, outputFile):
	#sudo tcpdump -G [seconds] -w [output_file] -W 2 -s 0 tcp and src net [192.168.0.0/16] and dst net ![192.168.0.0/16]
	#kick off the tcpdump with ring buffer
	#subprocess.Popen("tcpdump -G " + seconds + " -w " + outputFile + " -s 0 tcp and src net " + homeNetwork + " and dst net !" + homeNetwork, shell=True)

def usage():
	print "\nUsage: ./dlpTest.py [-n] [-r] [-s] [-o]"
	print "\th: help\n\tn: home network\n\tr: rules file\n\ts: seconds of traffic to capture\n\to: output file to capture to\n"

def main():
	try:
		opts, args = getopt.getopt(sys.argv[1:], "n:r:s:o:h")
	except getopt.GetoptError, err:
		print str(err) # will print something like "option -a not recognized"
		usage()
		sys.exit(2)
	homeNetwork = ""
	rulesFile = ""
	outputFile = "captured_output"
	seconds = 60
	
	#grab all of our arguments
	for o, a in opts:
		if o in ("-n", "--network"):
			homeNetwork = a
		elif o in ("-r", "--rules"):
			rulesFile = a
		elif o in ("-s", "--seconds"):
			seconds = int(a)
		elif o in ("-o", "--output"):
			outputFile = a
		elif o in ("-h", "--help"):
			usage()
			sys.exit()
		else:
			assert False, "unhandled option"

	if (homeNetwork == "" or rulesFile == ""):
		#require these specific arguments
		usage()
		sys.exit()

	startCapture(homeNetwork, seconds, outputFile)	
	time.sleep(3)
	monitorCapture(outputFile, seconds, rulesFile)


if __name__ == "__main__":
    main()
