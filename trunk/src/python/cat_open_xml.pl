#!/usr/bin/perl
############################################################################
#			cat_open_xml
############################################################################
# This script reads a document that is written in the OpenXML format, such
# as Microsoft Office documents and displays the content of that file, much
# like the antiword does for the older Microsoft Word format.
#
# See further information about the structure here:
# 	http://msdn.microsoft.com/en-us/library/aa338205.aspx
#
# This script requires both Archive::Zip and LibXML, 
# to install the dependencies using Ubuntu, issue the following commands:
# 	apt-get install libarchive-any-perl
# 	apt-get install libxml-libxml-perl
#
# According to the standard OpenXML documents are compressed using ZIP
# and therefore it is required to unzip the documents before parsing
# the content
# The content is then stored in a XML documents.  The file
# _rels/.rels defines the relationships that the document contains
# and therefore it should be the first file that is to be read.
# From there you can find any additional files that contain content 
#	DOC.ENDING/word/document.xml
#
# This script will read the _rels/.rels file, parse it's input, search
# for any XML file that contains text information of the file and 
# then parse that document and print out the content found
# in it.
#
# Usage:
#	cat_open_xml.pl DOCUMENT.ENDING
# Example:
#	cat_open_xml.pl readme.docx
#
# Author: Kristinn Gudjonsson
# Version : 0.1
# Date : 20/07/09
#
# Copyright 2009 Kristinn Gudjonsson, kristinn ( a t ) log2timeline ( d o t ) net
############################################################################## 

# define libraries
use strict;
use Archive::Zip qw( :ERROR_CODES );
use XML::LibXML;
use XML::LibXML::Common;
use Encode;

# define the needed variables
my $doc;
my $zip;
my $status;
my $xml;
my $metadata;
my $property;
my $propertylist;
my @properties;
my @relationships;
my $relationship;
my $parent;
my @list;
my $attrib;
my @targets;
my $encoding;

# see if the script was called with an argument
if( $#ARGV < 0 )
{
	print "Wrong usage: need to call script with an argument.\n";
	print "$0 - written by : Kristinn Gudjonsson, copyright 2009\n";
	print "Usage:\n\t$0 DOCUMENT.ending\nExample:\n\t$0 README.docx\n";
	exit 1;
}

# read the parameter (the document)
$doc = $ARGV[0];

# create a ZIP object
$zip = Archive::Zip->new();

# read the Word document, that is the ZIP file
$zip->read( $doc ) == AZ_OK or die "Unable to open Office file\n";

# extract document information
$status = $zip->extractMember( '_rels/.rels', '/tmp/rels.xml' );
die "Unable to extract schema from file, is it really a Office 2007 document (openXML)?\n" if $status != AZ_OK;

# read the rels file
$xml = XML::LibXML->new();
# read inn all the XML
$metadata = $xml->parse_file( "/tmp/rels.xml" );

# get all the Relationship nodes
$propertylist = $metadata->getDocumentElement();
@properties = $propertylist->childNodes;

# get the encoding of the document
$encoding = $metadata->encoding();

# examine each one
foreach $property (@properties)
{
	# property is a node
	if( $property->nodeType==ELEMENT_NODE )
	{
		# now we are inside the Relationship tag, find the type
		@relationships = $property->attributes();

		# examine each attribute that is defined for the relationshp
		foreach $relationship ( @relationships )
		{
			# we are trying to find nodes which contain property values for the file
			if( $relationship->toString =~ /.*Type.*ocument\"/ )
			{
				# now we have a property that consists of a property file
				# examine each attribute that is assigned to the parent node
				$parent = $relationship->getOwnerElement();
				
				@list = $parent->attributes();
				foreach $attrib ( @list ) 
				{
					# need to find the attribute Target, since that defines
					# the location of the XML document that describes the 
					# metadata information from the document
					if ( $attrib->toString =~ /Target/ )
					{
						# push the name of the metadata document into the array targets
						push( @targets, $attrib->value);
					}
				}
			}
		}
	}	
}
# we no longer need the rels.xml file, so we delete it
unlink( '/tmp/rels.xml' ); 

# examine all the targets
foreach $attrib (@targets)
{
	process_file( $attrib ) or die ( "Unable to read document contents\n");
}

# now we can exit the script gracefully
exit 0;

# ------------------------------------------------------------------------------------------------------------
#	process_file
# ------------------------------------------------------------------------------------------------------------
# This function reads a XML file that contains metadata
# information from a OpenXML file and prints out all the
# tags that are defined within it.
#
# @param xmlfile A string that contains the path within the ZIP archive that contains metadata information
# @param title A title for the file to be printed out before the metadata is printed
# @return Return false if unsuccessful, else true
sub process_file
{
	my $xmlfile;
	my $text;
	my $line;
	my @lines;
	my @attrs;
	my $attr;
	my $check;

	# assign the xmlfile
	$xmlfile = $_[0];
	
	$status = $zip->extractMember( $xmlfile, '/tmp/file.xml' ) ;
	die "Unable to extract content from file, is it really a Office 2007 document?\n" if $status != AZ_OK;

	# we can now read the file
	
	# create a XML parser
	$xml = XML::LibXML->new();
	
	# read inn all the XML
	$metadata = $xml->parse_file( "/tmp/file.xml" );
	
	$propertylist = $metadata->getDocumentElement();
	@properties = $propertylist->childNodes;
	
	foreach $property (@properties)
	{
		# property is a node
		if( $property->nodeType==ELEMENT_NODE )
		{
			# check if we are reading the body file of a Word document
			if( $property->nodeName =~ m/w:body/ )
			{
				# now get all the child values, which are lines
				@lines = $property->childNodes;

				foreach $line (@lines)
				{	
					$text =  encode( $encoding, $line->textContent );
					$text =~ s/\r/\n/g;
			

					print "$text\n";
				}
			}

			# check if we are reading an Excel document
			if( $property->nodeName =~ m/^sheets$/ )
			{
				print "This is an Excel file (no support for this document type yet)\n";
				# find all of the sheets that are included
				@lines = $property->childNodes;

				# read all the sheets used in the excel document
				foreach $line (@lines)
				{
					print "processing a sheet\n";
					@attrs = $line->attributes();

					# go through all of the attributes, searching for the name of the sheet
					foreach $attr (@attrs)
					{
						#print "an attribute value ", $attr->value, "\n";
						#print "an attribute has been found...", $attr->toString, "\n";
						if( $attr->toString =~ m/name/ )
						{
							# read the sheet itself
							# get the sheet name in correct format
							$text = lc( $attr->value );
	
							# now to process the file ( that is read the content )
						#	print "calling $text \n";
							process_file( "xl/worksheets/$text.xml" );
						}
					}
				}
			}
			
			if( $property->nodeName =~ m/worksheet/ )
			{
				print "In future version I would be processing a Work Sheet of this file\n";
			}
		}
	}
	
	unlink( '/tmp/file.xml' ); 

	print "returning from a call..\n";
	return 1;
}

