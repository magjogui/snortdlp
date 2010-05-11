'''
Created on May 11, 2010

@author: Will, Tyler
'''

class RuleCreator:
    
    
    def __init__(self, filename, substring):
        self.filename = filename
        self.substring = substring
    
    def addSnortRule(self):
        
        #open the snort file location for appending
        snortFile = open(file, 'a')
        self.sid = 1000000 + len(snortFile.readlines()) + 1
        self.createRule()
        
        snortFile.write("\n" + self.rule)
        
    def createRule(self):
        regex = self.createRegex()
        self.rule = "alert tcp $INTERNAL_NET any -> $EXTERNAL_NET any (msg: DLP" + self.filename + " alert”; pcre:\"" + regex + "\"; sid:" + self.sid + ";)"
        
    def createRegex(self):
        """
        Input: string of standardized input to create the regex from
        Output: regex we can plug into a Snort rule
        
        ToDo: write the method
        """
        
        words = self.substring.split()
        rule = ")( )* (".join(words)
        rule = "/(" + rule + ")/is"
        
        return rule
