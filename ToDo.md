# Initial Brainstorming #
#summary to do tasks

## Introduction ##

Various brainstormed tasks to start the project off.

## Initial Conceptual Considerations ##

  * Trigger external file processing from Snort (directly or with PCAP workaround)
    * Custom triggering alerts from outside of Snort
  * Look at packet captures for various methods and datatypes in exfiltration - how documents are broken up in packets
  * Dynamic rule generation
    * Rule chaining?
  * Keytext selection methods (histogram vs random?) depending on filetypes
    * The more unique words used the better a random sampling might be

## Specific Tasks ##

  1. Generate a Visio layout of the various components
  1. Start conceptual design of post-processing component (Visio)
  1. Start conceptual design of the keytext component (Visio?)
    * Decide how we're going to do the keytext selection
  1. Decide what types/extensions of data we're going to focus on first
    * Possibly: Microsoft Office docs (.xls, .ppt, .doc, etc), PDF, plaintext?
  1. Decide what language to build the keytext selection component in
  1. Decide what language to build the post-processing component in
  1. Decide where to start development

## Future Tasks ##

  * Design web frontend (PHP?)
  * Setup backend (MySQL)
  * Alert testing