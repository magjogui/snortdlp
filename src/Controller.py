'''
Created on May 11, 2010

@author: Will, Tyler
'''

from Histogram import Histogram
from RuleCreator import RuleCreator
import os

def addFile(snortFile, repositoryLocationFile, substringLength, fileToAdd):
    
    hist = Histogram(repositoryLocationFile, substringLength, fileToAdd)    
    substring = hist.selectSubstring()
    
    #if a unique substring is not found, don't add that rule
    if substring != "":
        rule = RuleCreator(snortFile, fileToAdd, substring)
        rule.addSnortRule()
        print "rule added for " + fileToAdd

def walkRepository(snortFile, repositoryLocationFile, substringLength):
    
    """
    Input: candidate unique substring
    Output: true if substring is in any file in the repository, false if not
    """
    
    # repositoryLocationFile contains the repository locations 
    locations = open(repositoryLocationFile, 'r').readlines()
    
    # crawl the path, checking if the substring is in each file
    for path in locations:
        for root, dirs, files in os.walk(path):
            for file in files:
                addFile(snortFile, repositoryLocationFile, substringLength, root+file)
        
    return False

def main():
    
    configLines = open("config").readlines()
    
    repositoryLocationFile = configLines[1].strip()
    substringLength = int(configLines[4])
    snortFile = configLines[7]
    
    """
    #fileToAdd needs to be an absolute path!!!
    fileToAdd = 'C:\\Users\\saintgosu\\Documents\\SnortDLP\\risk2.txt'

    hist = Histogram(repositoryLocationFile, substringLength, fileToAdd)    
    substring = hist.selectSubstring()
    
    #createRule(substring)
    rule = RuleCreator(snortFile, fileToAdd, substring)
    rule.addSnortRule()
    """
    
    walkRepository(snortFile, repositoryLocationFile, substringLength)
    
    print "\nfinished"
    

if __name__ == "__main__":
    main()