**From the "Scalable document fingerprinting" paper in 'Research' ->**
"We can significantly reduce false positives by using a substring selection strategy based on frequency measures. One way to do this is to compute the set of all substrings for the document and then pick the least frequently occurring substrings. However this is computationally expensive and does not yield good results because the space of substrings in one document is not a useful indication of the overall frequency of substrings."


Snort manual:http://www.snort.org/assets/140/snort_manual_2_8_6.pdf


For triggering outside rules:
> "The **logto** option tells Snort to log all packets that trigger this rule to a special output log file."
> Could we then post process the files (zip/whatnot) and then run another instance of Snort on the logged pcap files to generate the alerts?

> I found a Perl script that unzips a word .docx file and displays the specific file contents in plaintext: http://blog.kiddaland.net/2009/07/antiword-for-office-2007/

> The post also references a previous tool that does the same kind of thing for .doc files: http://www.winfield.demon.nl/

> An possible strategy here could be for the Snort signatures to search only for the headers of these specific formats in a reassembled TCP stream, then log upon detection. Then we could run a variety of scripts based on the specific header to extract plain text information from .docx/.ppt/.pdf/etc and run the textual match on that instead of the raw data. Thoughts?

> Also, once an alert is triggered the **tag** keyword allows for traffic involved the src/dst host(s) to be flagged and further analyzed
> If we have Snort configured to log to a database, **then in order to 'trigger' an outside alert after post-processing all we have to do it log an alert to the appropriate database table, independent of Snort**. This should show up in BASE/whatever frontend tool we're using.

To the problem of multiple packets:
> The **Stream5** preprocessor should be able to reassemble some packet streams (based on a variety of criteria) into a single pseudo-stream we can test on, so the issue of splitting a document over several packets might not be a problem

In addition to the "content" keyword for packet inspection, "pcre" allows for perl compatible regular expressions in payload searches.

For the generation of the dynamic rules, section 2.9 of the guide describes now to configure Snort to allow the reloading of the config file instead of a complete restart- we could possibly have a specific file containing the IP rules where we trigger a Snort config reload through a command line command after modifying the rule file.