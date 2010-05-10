'''
Created on May 10, 2010

@author: Will, Tyler
'''

def inRepository(substring):
    
    """
    Input: canidate unique substring
    Output: true if substring is in the repository, false if not
    """
    
    return False
    

def standardizeText(inputText):
    
    """
    Input: text string to standardize
    Output: input text stripped of everything except letters and numbers,
            each word separated by a single space
    
    ToDo: more standardization, i.e. stripping out punctuation etc.
    
    """
    
    return inputText.lower().split()

def selectSubstring(histogram, substringLength, inputText):
    
    """
    Input: histogram to score by, text string to score
            text is raw
            substringLength = length in words of the substring to select
    Output: substring to search for
    
    Issue: if the text is standardized, the substring needs to not be-
            we have to search for the exact substring in the Snort rules!
            So we need to identify where the substring occurs and grab the 
            raw version of that substring
    """
    
    inputText = standardizeText(inputText)
    
    lowSubstring = ""
    lowScore = 1000000000
    
    # Iterate through standardized text by word
    # Select the substring with the lowest score
    for x in range(0,len(inputText)-substringLength+1):
        substring = " ".join(inputText[x:x+substringLength])
        score = scoreText(histogram,substring)
        
        print substring,":",score
        
        # if we've found a new lowest-scored substring,
        # make sure it is not in the IP repository
        if score < lowScore:
            if not inRepository(lowSubstring):
                lowScore = score
                lowSubstring = substring
    
    if lowSubstring == "":
        print "Unique substring not found: consider increasing substring length"
    
    return lowSubstring
    
def scoreText(histogram,textToScore):
    
    """
    Input: histogram to score by and text string to score
    Output: score S of summed frequencies of words in text
    """
    
    text = standardizeText(textToScore)
    score = 0
    
    # Add known word scores from the histogram to the score
    for word in text:
        if word in histogram:
            score += histogram[word]
    
    return score
    
def returnHistogram(inputText):
    
    """
    Input: string of numbers/words
    Output: dictionary of words:frequencies
    """
    
    #convert input text to lowercase and split by spaces
    text = standardizeText(inputText)
    histogram = dict()
    
    # Build the histogram from the input text
    for word in text: 
        #update key=word in histogram with the count of that word
        histogram.update( { word : text.count(word) } ) 
    
    return histogram
    

def main():
    
    histogramText = "THe 13 quick quick BROWN    foxes the the 1234 jump jump"
    
    histogram = returnHistogram(histogramText)
    print histogram,"\n"
    
    #print "Text score = " + scoreText(histogram, "the foxes")
    stringToTest = "the quick 1234 brown foxes jump"
    lowestSubstring = selectSubstring(histogram, 3, stringToTest)
    print "\nLowest scored substring: ",lowestSubstring

if __name__ == "__main__":
    main()
