'''
Created on May 10, 2010

@author: Will, Tyler
'''

import os, re

class Histogram:

    def __init__(self, repositoryLocations, subLength, fileToAdd):
        """Constructor for Histogram: sets up class variables and kicks off histogram generation.
        
        Keyword arguments:
        repositoryLocations -- list of strings of the paths to each repository location
        subLength -- length of the unique substring to select
        fileToAdd -- path of the input file to generate the substring from
        """
        # setup class variables
        self.repositoryLocations = repositoryLocations
        self.substringLength = subLength
        self.histogram = dict()
        self.fileName = fileToAdd
        
        #for .doc and other files, do we need to read the file in under binary mode ('rb')?
        self.inputText = self.standardizeText(open(self.fileName).read())
        
        #generate the histogram from the input text
        self.genHistogram()
        
        #add the local histogram to the global histogram table
        self.addHistogramToGlobal()

    def repositoryScore(self, substring):
        """Return a score for the substring using the repository histogram.
        
        Keyword arguments:
        substring -- input substring to score

        Returns:
        The score of the substring.
        
        ToDo: implement database connection/lookup, implement this method
        """
        
        return 0
    
    def addHistogramToGlobal(self):
        """Add the internal histogram to the global histogram database.

        ToDo: Add the local histogram frequencies to the global histogram database, implement
        """
        
        return 0

    def inFile(self, path, substring):
        """Determine if a given substring is in a specified file.
        
        Keyword arguments:
        path -- path of the file to search through
        substring -- substring to search for
        """
        
        #pull the text from the specified file and standardize the text
        text = self.standardizeText(open(path,'rb').read())

        return substring in text
    
    def inRepository(self, substring):
        """Check if a specific substring is in any file in the repository.
        
        Keyword arguments:
        substring -- substring to search the repository for
        
        Returns:
        true if substring is in any file in the repository, false if not
        """
        # crawl the path, checking if the substring is in each file
        for path in self.repositoryLocations:
            for root, dirs, files in os.walk(path):
                for file in files:
                    if root+file != self.fileName: #skip this file when searching existing repository
                        if self.inFile(root+file, substring):
                            return True
            
        return False
    
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
    
    def selectSubstring(self):
        """Select the lowest scored substring from the input text.
        
        Returns: unique selected substring or an empty string if a unique string is not found
        
        selectSubstring() generates a weighted local/global histogram score
        for every possible specified length substring in the input text.
        It then returns the lowest scored substring not in the repository, or
        and empty string if a substring not in the repository wasn't found.
        """
        
        alpha = 1 #weight for local histogram score
        beta = .5 #weight for repository histogram score
        
        #create a list to hold a tuple for each substring consisting of
        # (startLocation, score) for each substring examined
        substringScores = list()
        
        #split standardized input text into a list of individual words
        inputText = self.inputText.split()
        
        # Iterate through standardized text by word
        # Select the substring with the lowest score
        for x in range(0,len(inputText)-self.substringLength+1):
            
            #grab out a substring of the correct number of words
            substring = " ".join(inputText[x:x+self.substringLength])
            
            #score the substring and add it to the list of scores
            score = alpha*self.localScore(substring) + beta*self.repositoryScore(substring)
            substringScores.append((x,score))
        
        #sort the scored list by the second element in the tuple (score)
        sortedScores = sorted(substringScores, key=lambda x: x[1])
        foundLow = False
        
        # find the lowest scored substring that isn't in the IP repository
        # do this by iterating over the sorted scored substring list from the beginning 
        for (startLocation, score) in sortedScores:
            substring = " ".join(inputText[startLocation:startLocation+self.substringLength])
            if not self.inRepository(substring):
                foundLow = True
                return substring
        
        if not foundLow:
            print "Substring not found for " + self.fileName + ": consider increasing substring length"
        
        return ""
        
    def localScore(self, substring):
        """Return a score of a specific substring using the local histogram.
        
        Keyword arguments:
        substring -- specific substring to score
        
        Returns:
        score S of summed frequencies of words in text
        """
        
        #standardize our text and split it into a list of individual words
        text = self.standardizeText(substring).split()
        score = 0
        
        # Add known word scores from the histogram to the score
        for word in text:
            if word in self.histogram:
                score += self.histogram[word]
        
        return score
        
    def genHistogram(self):
        """Generate the histogram of the inputText.
        """
        
        #split standardized input text into a list of individual words
        text = self.inputText.split()
        
        # Build the histogram from the input text
        for word in text: 
            #update key=word in histogram with the count of that word
            self.histogram.update( { word : text.count(word) } ) 