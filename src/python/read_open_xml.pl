#!/usr/bin/perl
############################################################################
#			read_open_xml
############################################################################
# This script reads a document that is written in the OpenXML format, such
# as Microsoft Office documents and displays the metadata information that
# are contained in it according to the documentation that Microsoft provides
#
# See further information about the structure here:
# 	http://msdn.microsoft.com/en-us/library/aa338205.aspx
# 
# Also some information about custom properties:
#	http://msdn.microsoft.com/en-us/library/bb447589.aspx
#
# This script requires both Archive::Zip and LibXML, 
# to install the dependencies using Ubuntu, issue the following commands:
# 	apt-get install libarchive-any-perl
# 	apt-get install libxml-libxml-perl
#
# According to the standard OpenXML documents are compressed using ZIP
# and therefore it is required to unzip the documents that contain the
# metadata information before processing them further.  
# The metadata is then stored in a XML documents.  The file
# _rels/.rels defines the relationships that the document contains
# and therefore it should be the first file that is to be read.
# From there you can find any additional files that contain metadata
# information, most files will contain two metadata information files:
#	DOC.ENDING/docProps/app.xml
#	DOC.ENDING/docProps/core.xml	
#
# This script will read the _rels/.rels file, parse it's input, search
# for any XML file that contains property information of the file and 
# then parse that document and print out the metadata information found
# in it.
#
# Usage:
#	read_open_xml.pl DOCUMENT.ENDING
# Example:
#	read_open_xml.pl readme.docx
#
# Author: Kristinn Gudjonsson
# Web site: blog.kiddaland.net
# Version : 0.2
# Date : 23/09/09
#
# Copyright 2009 Kristinn Gudjonsson, kristinn ( a t ) log2timeline ( d o t ) net
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
############################################################################## 

# define libraries
use strict;
use Archive::Zip qw( :ERROR_CODES );	# to read zip archive
use XML::LibXML;			# to read XML
use XML::LibXML::Common;		# For XML reading
use Encode;				# to encode characters correctly
use Pod::Usage;				# for POD

use vars qw($VERSION);

# version number
$VERSION = '0.2';

# define the needed variables
my $doc;		# the name of the document
my $zip;		# a zip object
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

# for verification
my %info;
my $temp;
my @n;

##########  CHECK PARAMETERS  ##########  
# see if the script was called with an argument
pod2usage( {
	-message        => "Wrong usage: need to call script with an argument\n",
	-verbose        => 1,
	-exitval        => 1  }) if $#ARGV < 0;


# read the parameter (the document)
$doc = shift;

# check if the file exists or not
pod2usage( {
	-message        => "The file $doc does not exist",
	-verbose        => 1,
	-exitval        => 12  }) unless -e $doc;


##########  VERIFY  ##########  
# verify the structure of the file before moving on
open( FH, $doc );
binmode(FH);

# read the header (30 bytes + name)
seek( FH, 0, 0 );
read(FH,$temp,30);

# and the to parse the header into appropriate variables
($info{'magic'},$info{'version'},$info{'general'},$info{'comp_method'},$info{'last_mod_time'},$info{'last_mod_date'},$info{'crc2'},$info{'compr_size'},$info{'size'},$info{'filename_length'},$info{'extra_length'} ) = unpack( "VvvvvvVVVvv", $temp );

# now to read the name part
for( my $i=0; $i < $info{'filename_length'} ; $i++ )
{	
	seek(FH,30+$i,0);
	read(FH,$temp,1);

	push(@n,$temp); 
}

# join the name part
$info{'filename'} = join('',@n);
# remove control characters
$info{'filename'} =~ s/[[:cntrl:]]//g;

close(FH);

# now to verify structure
if( $info{'magic'} eq 0x04034b50 )
{
	# this is an ZIP archive, now we need to confirm that this is an OpenXML document
	if( $info{'filename'} !~ m/Content_Types/ )
	{
		pod2usage( {
			-message        => 'The file (' . $doc . ') is a ZIP file but not an OpenXML document',
			-verbose        => 1,
			-exitval        => 2  });
	}
}
else
{
	pod2usage( {
		-message        => "This document( $doc ) is not a ZIP archive and therefore cannot be an OpenXML document",
		-verbose        => 1,
		-exitval        => 2  });
}

##########  START PARSING  ##########  
# create a ZIP object
$zip = Archive::Zip->new();

# read the Word document, that is the ZIP file
$zip->read( $doc ) == AZ_OK or pod2usage( {
		-message        => "Unable to open Office file", 
		-verbose        => 1,
		-exitval        => 2  });

# extract document information
$status = $zip->extractMember( '_rels/.rels', '/tmp/rels.xml' );

# check status
pod2usage( {
	-message        => "Unable to extract schema from file, is it really a Office 2007 document (openXML)?",
	-verbose        => 1,
	-exitval        => 2  }) if $status != AZ_OK;

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
			if( $relationship->toString =~ /.*Type.*prop.*/ )
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
	
			if( $relationship->toString =~ /officeDocument\"/ )
			{
				# now we have a definition of the type of document
				$parent = $relationship->getOwnerElement();
				@list = $parent->attributes();
				foreach $attrib (@list)
				{
					if( $attrib->toString =~ /Target/ )
					{
						( $info{'doc_type'}, $info{'doc_xml'} ) = split( /\//, $attrib->value );

						if( $info{'doc_type'} eq 'xl' )
						{
							$info{'doc_type'} = 'Excel';
						}
						elsif( $info{'doc_type'} eq 'ppt' )
						{
							$info{'doc_type'} = 'Powerpoint';
						}
					}
				} 
			}
		}
	}	
}
# we no longer need the rels.xml file, so we delete it
unlink( '/tmp/rels.xml' ); 

# start by print out general information
print "==========================================================================
	cmd line: $0 $doc
==========================================================================

Document name: $doc
Current Date: ", `date`, "\n";

#print 'ZIP version: ' . $info{'version'} . "\n";
#print "ZIP filename: " . $info{'filename'} . "\n";

print "This is a " . $info{'doc_type'} . " document\n\n";

# examine all the targets
foreach $attrib (@targets)
{
	# check each property file and process it
	if( $attrib eq "docProps/core.xml" )
	{
		process_file( $attrib, "File Metadata" ) or die ( "Unable to read file metadata\n");
	}
	elsif( $attrib eq "docProps/app.xml" )
	{
		process_file( $attrib, "Application Metadata") or die ( "Unable to read application metadata\n" );
	}
	else
	{
		# unkown property file, let's process it anyway
		process_file($attrib, "Custom Metadata") or die ("Unable to read custom metadata, $attrib\n");
	}
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
	my @splits;
	my $xmlfile;
	my $title;
	my $child;
	my @children;
	my $text;
	my $hasChild;
	my $a_text;	# attribute text
	my @attr;

	# assign the xmlfile
	$xmlfile = shift;
	$title = shift;
	
	$status = $zip->extractMember( $xmlfile, '/tmp/file.xml' ) ;
	die "Unable to extract MetaData from file, is it really a Office 2007 document?\n" if $status != AZ_OK;
	
	# we can now read the file
	
	# create a XML parser
	$xml = XML::LibXML->new();
	
	# read inn all the XML
	$metadata = $xml->parse_file( "/tmp/file.xml" );
	
	$propertylist = $metadata->getDocumentElement();
	@properties = $propertylist->childNodes;
	
	# print some header information
	print "--------------------------------------------------------------------------\n";
	print "$title\n";
	print "--------------------------------------------------------------------------\n";
	foreach $property (@properties)
	{
		$text = '';
		$hasChild = 0;
		$a_text = '';

		# property is a node
		if( $property->nodeType==ELEMENT_NODE )
		{
			# we need to test if there is a structure below this one
			$child = ($property->childNodes)[0];

			if( defined $child && $child->hasChildNodes() )
			{
				@children = $child->childNodes;
				$hasChild = 1;
		
				foreach (@children)
				{
					$text .= $_->textContent . ", ";
				}
				$text =~ s/, $//g;
				
			}

			# check if there are any attributes
			if( $property->hasAttributes() )	
			{
				@attr = $property->attributes();
				foreach (@attr)
				{
					$a_text .= $_->nodeName . ' = ' . $_->value . ', '; 
				}
				$a_text =~ s/, $//g;
			}

			@splits = split( ':', $property->nodeName );

			# print the MetaData information
			print "\t", $splits[1] if $#splits eq 1;
			print "\t", $splits[0] unless $#splits eq 1;
	
			print " ($a_text)" unless $a_text eq '';

			print " = ",  encode( $encoding, $text), "\n" if $hasChild;
			print " = ",  encode( $encoding, $property->textContent), "\n" unless $hasChild;
		}
	}
	
	unlink( '/tmp/file.xml' ); 

	return 1;
}

1;

__END__

=pod

=head1 NAME

B<read_open_xml> - A script to read the metadata information from an OpenXML document, the standard that Office 2007 and later use. 

=head1 SYNOPSIS 

B<read_open_xml> DOCUMENT

=head1 DESCRIPTION

This script reads a document that is written in the OpenXML format, such as Microsoft Office documents and displays the metadata information that are contained in it according to the documentation that Microsoft provides.

See further information about the structure here:

=over 8

http://msdn.microsoft.com/en-us/library/aa338205.aspx

=back

And some information about custom properties

=over 8

http://msdn.microsoft.com/en-us/library/bb447589.aspx

=back

According to the standard OpenXML documents are compressed using ZIP and therefore it is required to unzip the documents that contain the metadata information before processing them further.  The metadata is then stored in a XML documents.  The file _rels/.rels defines the relationships that the document contains and therefore it should be the first file that is to be read. From there you can find any additional files that contain metadata information, most files will contain two metadata information files:

=over 8

DOC.ENDING/docProps/app.xml

DOC.ENDING/docProps/core.xml	

=back

This script will read the _rels/.rels file, parse it's input, search for any XML file that contains property information of the file and then parse that document and print out the metadata information found in it.

=head1 DEPENDENCIES

This script requires both Archive::Zip and LibXML, to install the dependencies using Ubuntu, issue the following commands:

=over 8

apt-get install libarchive-any-perl

apt-get install libxml-libxml-perl

=back

On some versions of Ubuntu and Debian the package libarchive-zip-perl exists instead of the libarchive-any-perl.

=head1 METHODS

=over 8

=item B<process_file( xmlfile, title )>

This sub routine takes two arguments; xmlfile, a path to the XML file inside the OpenXML document and title, a string containing description of the XML file being parsed.

The sub routine returns either a true or false value depending whether it executed successfully or not

=back

=head1 AUTHOR

Kristinn Gudjonsson <kristinn (a t) log2timeline ( d o t ) net> is the original author of the program.

The script is released under GPL so anyone can contribute to it.  

=cut

