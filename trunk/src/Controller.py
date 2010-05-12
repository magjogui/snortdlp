'''
Created on May 11, 2010

@author: Will, Tyler
'''

from Histogram import Histogram
from RuleCreator import RuleCreator
import os

def addFile(snortFile, repositoryLocations, substringLength, fileToAdd):
    """Create a snort rule for a specified input file.

    Keyword arguments:
    snortFile -- path to the file to write snort rules out to
    repositoryLocations -- list of strings of the paths to each repository location
    substringLength -- length of the unique substring to select
    fileToAdd -- path of the input file the substring was generated from
    """
    
    #old: hist = Histogram(repositoryLocationFile, substringLength, fileToAdd)
    hist = Histogram(repositoryLocations, substringLength, fileToAdd)
    substring = hist.selectSubstring()
    
    #if a unique substring is not found, don't add that rule
    if substring != "":
        rule = RuleCreator(snortFile, repositoryLocations, fileToAdd, substring)
        rule.addSnortRule()
        rule.regexInRepository()
        print "rule added for " + fileToAdd

def crawlRepository(snortFile, repositoryLocations, substringLength):
    """Crawl the repository locations and create rules for each found file.

    Keyword arguments:
    snortFile -- path to the file to write snort rules out to
    repositoryLocations -- list of strings of the paths to each repository location
    substringLength -- length of the unique substring to select
    """

    # crawl the path, checking if the substring is in each file
    for path in repositoryLocations:
        for root, dirs, files in os.walk(path):
            for file in files:
                addFile(snortFile, repositoryLocations, substringLength, root+file)
    
def main():
    
    configurationFile = "config"
    configLines = open(configurationFile).readlines()
    
    repositoryLocations = list()
    
    for line in configLines:
        elements = line.split(":")
        
        #pull the arguments out of the configuration file
        if len(elements) > 1: #if this is a configuration line with ":"
            if elements[0] == "Substring length" :
                substringLength = int(line[17:])
            elif elements[0] == "Snort rule file":
                snortFile = line[16:].strip()
            elif elements[0] == "location":
                repositoryLocations.append(line[9:].strip())

    crawlRepository(snortFile, repositoryLocations, substringLength)
    
    print "\nfinished"
    
if __name__ == "__main__":
    main()