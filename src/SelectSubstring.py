'''
Created on May 10, 2010

@author: Will, Tyler
'''


'''
Proposed methodology for substring selection:
1. Take each document and break it into discrete words/numbers (we can determine a way to standardize excel documents, databases, etc.)

2. Enter each word into a global histogram stored in MySQL and into a document-specific histogram stored temporarily.

3. To select the substring of each document, iterate through 5-10 word substrings(determine the length with experimentation). Add together the frequencies of each word from the global histogram to get a 'global' score for that substring and repeat with the document-only data to get a 'local' score.

4. Using some kind of weighting, select the substring with the lowest combined global/local frequency score as the uniquely identifiable substring:

G = substring global hist score, L = substring local hist score.
min(T = alpha*G + beta*L).

5. Search for this substring in the IP repository. If found, select the substring with the next lowest score repeat until the selected substring is not found in the repository.  
'''


def standardizeText(inputText):
    
    """
    Input: text string to standardize
    Output: input text stripped of everything except letters and numbers,
            each word separated by a single space
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
            So we need to identify where the substring occurs
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
        
        # if we've found a new lowest-scored substring...
        if score < lowScore:
            lowScore = score
            lowSubstring = substring
    
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
