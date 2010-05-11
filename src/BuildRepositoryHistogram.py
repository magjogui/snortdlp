'''
Created on May 10, 2010

@author: Will, Tyler

Take datastore locations from "locations.txt".
Crawl the documents in each location and input the word frequencies 
from each file into the global database histogram.

'''

import os
#import MySQLdb need to install!

# MySql database interface = MySQLdb
# http://mysql-python.sourceforge.net/ 

def standardizeText(inputText):
    
    """
    Input: text string to standardize
    Output: string input text stripped of everything except letters and numbers,
            each word separated by a single space
    
    ToDo: more standardization, i.e. stripping out punctuation etc.
    
    """
    
    return " ".join(inputText.lower().split())

def addToHistogram(filePath):
    """
    Input: absolute path of file to add to the histogram table
    Output: none
    
    Add a given file at filePath to the global histogram database
    
    ToDo: implement database connection, insert into table, etc.
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
    
    """
    Input: path of location to crawl files
    Output: none
    
    Crawl the filepath given and add each file to the global histogram
    """
    
    for root, dirs, files in os.walk(path):
        for file in files:
            addToHistogram(root+file)
        
def crawlLocations(repositoryLocationFile):
    
    """
    Input: location of file containing repository locations
    Output: none
    """
    
    # repositoryLocationFile contains the repository locations 
    locations = open(repositoryLocationFile, 'r').readlines()
    
    for path in locations:
        walkLocation(path)

