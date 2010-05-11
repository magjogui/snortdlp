'''
Created on May 11, 2010

throwaway testing class

'''

def sanitizeRegex(word):
    reserved = "[\^$.|?*+(){}"
    for char in reserved:
        print char
        word = word.replace(char,"\\"+char)
        print word
        
    return word
