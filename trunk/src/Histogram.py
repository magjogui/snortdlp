'''
Created on May 10, 2010

@author: Will, Tyler
'''

import os, re

class Histogram:
    
    def __init__(self, locFile, subLength, fileToAdd):
        
        # setup class variables
        self.repositoryLocationFile = locFile
        self.substringLength = subLength
        self.histogram = dict()
        self.fileName = fileToAdd
        
        #for .doc and other files, we need to read the file in under binary mode ('rb')
        #otherwise the non-ascii characters mess up the viewing
        #regex to strip out all non-ascii characters:
        """rawFileText = open(self.fileName,'rb').read()
        regex = re.compile("[^A-Za-z 0-9 \.,\?'""!@#\$%\^&\*\(\)-_=\+;:<>\/\\\|\}\{\[\]`~]*")
        
        print rawFileText[:50]
        # replace with "" or " "? Preserve whitepace?
        cleanText = regex.sub("",rawFileText)"""
        
        
        self.inputText = self.standardizeText(open(self.fileName).read())
        
        #generate the histogram from the input text
        self.genHistogram()

    def repositoryScore(self, substring):
        
        """
        Input: input substring to score from repository histogram
        Output: score of substring from repository histogram
        
        ToDo: implement database connection/lookup, write the method
        """
        
        return 0
    
    def inFile(self, path, substring):
        
        """
        Input: path of file to search through and substring to search for 
        Output: True if substring is in standardized file text, false otherwise
        """
        
        #pull the text from the specified file and standardize the text
        text = self.standardizeText(open(path,'rb').read())

        return substring in text
    
    def inRepository(self, substring):
        
        """
        Input: candidate unique substring
        Output: true if substring is in any file in the repository, false if not
        """
        
        # repositoryLocationFile contains the repository locations 
        locations = open(self.repositoryLocationFile, 'r').readlines()
        
        # crawl the path, checking if the substring is in each file
        for path in locations:
            for root, dirs, files in os.walk(path):
                for file in files:
                    if root+file != self.fileName: #skip this file when searching existing repository
                        if self.inFile(root+file, substring):
                            return True
            
        return False
    
    def standardizeText(self, inputText):
        
        """
        Input: text string to standardize
        Output: string input text stripped of everything except letters and numbers,
                each word separated by a single space
        
        ToDo: more standardization, i.e. stripping out punctuation etc.
        
        """
        
        return " ".join(inputText.lower().split())
    
    def selectSubstring(self):
        
        """
        Input: none
        Output: unique substring to search for
        
        selectSubstring() generates a weighted local/global histogram score
        for every possible specified length substring in the input text.
        It then returns the lowest scored substring not in the repository, or
        and empty string if a substring not in the repository wasn't found.
        """
        
        #standardize our text and split it into a list of individual words
        #inputText = self.standardizeText(inputText).split()
        
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
                print "Unique substring for " + self.fileName + ": " + substring
                return substring
        
        if not foundLow:
            print "Not found for " + self.fileName + ": consider increasing substring length"
        
        return ""
        
    def localScore(self, substring):
        
        """
        Input: substring to score
        Output: score S of summed frequencies of words in text
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
        
        """
        Generate the histogram of the inputText
        """
        
        #split standardized input text into a list of individual words
        text = self.inputText.split()
        
        # Build the histogram from the input text
        for word in text: 
            #update key=word in histogram with the count of that word
            self.histogram.update( { word : text.count(word) } ) 