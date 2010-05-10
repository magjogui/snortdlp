'''
Created on May 10, 2010

@author: Will, Tyler

Take datastore locations from "locations.txt".
Crawl the documents in each location and input the word frequencies 
from each file into the global database histogram.

'''

import os
#import MySQLdb need to install!
from SelectSubstring import returnHistogram


# MySql database interface = MySQLdb
# http://mysql-python.sourceforge.net/ 


def addToHistogram(filePath):
    """
    Input: absolute path of file to add to the histogram table
    Output: none
    
    Add a given file at filePath to the global histogram database
    """
    
    #connect to database
    
    #Read in given file and build a histogram from the text
    inputText = open(filePath,'rb').read()
    histogram = returnHistogram(inputText)
    
    print histogram
    
    #input histogram into database
    

def walkLocation(path):
    
    """
    Input: path of location to crawl files
    Output: none
    
    Crawl the filepath given and add each file to the global histogram
    """
    
    for root, dirs, files in os.walk(path):
        for file in files:
            addToHistogram(root+file)
        
def main():
    
    # "locations.txt" contains the repository locations 
    locations = open("locations.txt", 'r').readlines()
    
    for path in locations:
        walkLocation(path)
    

if __name__ == "__main__":
    main()
