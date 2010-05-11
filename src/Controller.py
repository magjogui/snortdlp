'''
Created on May 11, 2010

@author: Will, Tyler
'''

from Histogram import Histogram
from RuleCreator import RuleCreator

def main():
    
    configLines = open("config").readlines()
    
    repositoryLocationFile = configLines[1].strip()
    substringLength = int(configLines[4])
    snortFile = configLines[7]
    
    #fileToAdd needs to be an absolute path!!!
    fileToAdd = 'C:\\Users\\saintgosu\\Documents\\SnortDLP\\risk2.txt'

    hist = Histogram(repositoryLocationFile, substringLength, fileToAdd)    
    substring = hist.selectSubstring()
    
    print substring
    #createRule(substring)
    rule = RuleCreator(fileToAdd, substring)
    

if __name__ == "__main__":
    main()