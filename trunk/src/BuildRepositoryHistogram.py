'''
Created on May 10, 2010

@author: Will, Tyler
'''

import os
#import MySQLdb #need to install!!!

# MySql database interface = MySQLdb
# http://mysql-python.sourceforge.net/ 

def standardizeText(self, inputText):
    """Standardizes input text.
    
    Keyword arguments:
    inputText -- text string to standardize
    
    Returns:
    string input text stripped of everything except letters and numbers, each word separated by a single space
    
    ToDo: should we use regular expressions to strip all non-ascii out?
    """
    
    #regex to strip out all non-ascii characters:
    #rawFileText = open(self.fileName,'rb').read()
    #regex = re.compile("[^A-Za-z 0-9 \.,\?'""!@#\$%\^&\*\(\)-_=\+;:<>\/\\\|\}\{\[\]`~]*")
    #cleanText = regex.sub("",rawFileText)
    
    return " ".join(inputText.lower().split())

def addToHistogram(filePath):
    """Add a specific file to the global histogram.
    
    Keyword arguments:
    filePath -- path of the file to add to the global histogram

    ToDo: implement database connection, insert into table, implement
    """
    
    #Read in given file and build a histogram from the text
    inputText = open(filePath,'rb').read()
    histogram = dict()
    
    #split standardized input text into a list of individual words
    text = standardizeText(inputText).split()
    
    # Build the histogram from the input text
    for word in text: 
        #update key=word in histogram with the count of that word
        histogram.update( { word : text.count(word) } ) 
    
    #input histogram into database

def walkLocation(path):
    
    """Crawl a specific location and add each file to the histogram.
    
    Keyword arguments:
    path -- absolute path of the file to add to the histogram
    """
    
    for root, dirs, files in os.walk(path):
        for file in files:
            addToHistogram(root+file)
        
def crawlLocations(repositoryLocations):
    
    """Crawl each repository location and add each file.
    
    Keyword arguments:
    repositoryLocations -- list of strings of the paths to each repository location
    """
   
    for path in repositoryLocations:
        walkLocation(path)

