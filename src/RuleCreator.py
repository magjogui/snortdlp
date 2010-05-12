'''
Created on May 11, 2010

@author: Will, Tyler
'''

import os, re

class RuleCreator:
    
    def __init__(self, snortFile, repositoryLocations, filename, substring):
        """Construtor for RuleCreator: sets class variables.
        
        Keyword arguments:
        snortFile -- path to the file to write snort rules out to
        repositoryLocations -- list of strings of the paths to each repository location
        filename -- path of the input file the substring was generated from
        substring -- unique substring to create the rule from
        """
        self.filename = filename
        self.substring = substring
        self.snortFile = snortFile
        self.repositoryLocations = repositoryLocations
    
    def regexInRepository(self):
        """Checks if the generated regular expression matches any file in the repository.
        
        Returns: True if the regex is matched against a file in the repository, false otherwise
        """
        
        regex = re.compile(self.regex)
        
        for path in self.repositoryLocations:
            for root, dirs, files in os.walk(path):
                for file in files:
                    if root+file != self.filename: #skip this file when searching existing repository
                        text = open(root+file,'rb').read()
                        if regex.search(text) != None: #if we match the regex against a file in the repository
                            return True
        return False
    
    def addSnortRule(self):
        """Add the generated rule to the snort rule file.
        """
        
        lines = ""
        if os.path.exists(self.snortFile):
            #if the rule file already exists, count the number of existing rules
            #in order to determine the correct sid
            file = open(self.snortFile, 'r')
            lines = file.readlines()
        self.sid = 1000000 + len(lines) -4
        
        self.createRule()
        
        file = open(self.snortFile, 'a')
        file.write(self.rule + "\n")
    
    def sanitizeRegex(self, word):
        """Escape out of any reserved characters in a input regular expression.
        
        Keyword arguments:
        word -- the text of the regular expression to sanitize
        
        Returns:
        The input word sanitized of reserved regex characters.
        """
        
        reserved = "[\\^$.|?*+(){}"
        for char in reserved:
            word = word.replace(char,"\\"+char)
        
        return word
    
    def createRule(self):
        """Create the rule with proper Snort syntax.
        
        Returns:
        The created Snort rule.
        """
        
        self.createRegex()
        self.rule = "alert tcp $HOME_NET any -> $EXTERNAL_NET any (msg: \"DLP: " + str(self.filename) + " alert\"; pcre:\"" + str(self.regex) + "\"; classtype:data-loss; sid:" + str(self.sid) + ";)"
        
        return self.rule
        
    def createRegex(self):
        """Creates a regular expression from the input substring.
        """
        
        words = self.substring.split()
        cleanWords = [self.sanitizeRegex(word) for word in words]
        
        rule = ")( )*(".join(cleanWords)
        rule = "/(" + rule + ")/is"
        
        self.regex = rule
        
        return self.regex
