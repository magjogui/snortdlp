'''
Created on May 11, 2010

@author: Will, Tyler
'''

import os

class RuleCreator:
    
    
    def __init__(self, snortFile, filename, substring):
        self.filename = filename
        self.substring = substring
        self.snortFile = snortFile
    
    def addSnortRule(self):
        
        #open the snort file location for appending
        lines = ""
        if os.path.exists(self.snortFile):
            file = open(self.snortFile, 'r')
            lines = file.readlines()
        self.sid = 1000000 + len(lines) -4
        
        self.createRule()
        
        file = open(self.snortFile, 'a')
        file.write(self.rule + "\n")
    
    def sanitizeRegex(self, word):
        
        """
        Input: word used to build regex expression
        Output: word sanitized of all reserved regex charaters
        """
        
        reserved = "[\\^$.|?*+(){}"
        for char in reserved:
            word = word.replace(char,"\\"+char)
        
        return word
    
    def createRule(self):
        regex = self.createRegex()
        self.rule = "alert tcp $HOME_NET any -> $EXTERNAL_NET any (msg: \"DLP: " + str(self.filename) + " alert\"; pcre:\"" + str(regex) + "\"; classtype:data-loss; sid:" + str(self.sid) + ";)"
        
    def createRegex(self):
        """
        Input: string of standardized input to create the regex from
        Output: regex we can plug into a Snort rule
        
        ToDo: write the method
        """
        
        words = self.substring.split()
        cleanWords = [self.sanitizeRegex(word) for word in words]
        
        rule = ")( )* (".join(cleanWords)
        rule = "/(" + rule + ")/is"
        
        return rule
