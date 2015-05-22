## DOC ##

  * Antiword (http://www.winfield.demon.nl/): Extracts text from DOC files and tries to retain some of the formatting
  * Catdoc (http://freshmeat.net/projects/catdoc/): down and dirty, extracts text from DOCs
  * wvWare (http://wvware.sourceforge.net/): suite of command line tools to extract information from work docs


## XML Office Documents ##

  * "Antiword for Office 2007" (http://blog.kiddaland.net/2009/07/antiword-for-office-2007/): perl script to extract text from .docx...could we tweak to do xlsx/pptx also?
  * Using existing utilities?:
    * $ unzip document.docx /word/document.xml
    * $ xmllint ---html ---htmlout document.xml > document.html

## PDF ##

  * pdftotxt: open source linux command line utility that takes a PDF input and outputs extracted text. Built into most distributions.
  * PDFMiner (http://www.unixuser.org/~euske/python/pdfminer/index.html): Python library to mine PDF data from python


## Misc ##

  * Decent page on various tools: http://dataconv.org/apps_office.html
  * Using Java and open office to convert doc/ppt/xls to pdf: http://www.dancrintea.ro/doc-to-pdf/   (can we then convert to txt?)