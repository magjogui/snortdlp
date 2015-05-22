## Overview ##
SnortDLP a.k.a. "Pig Pen" is an open source data loss prevention project that utilizes Snort to detect the exfiltration of sensitive data.

## Features ##

Web based application
  * Written in PHP and utilizes a MySQL backend for cross operating system portability
  * Administrative login to protect unauthorized access
  * Determines a unique fingerprint for
    * free text
    * individual documents
    * each document in a repository of sensitive documents
    * database tables (future)
  * Supports plain text documents (including doc, ppt, etc) and emails
  * Generates Perl-compatible regular expressions (PCREs) and automatically adds a custom snort rule for each document or file
  * Detects and alerts administrators through a Snort interface
  * Flagging and carving out zip/pdf files based on file headers
    * Office 2007 (docx, pptx, xlsx) support
    * PDF support
## Future ##
  * Email integration