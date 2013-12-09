# Content services module

Provides abstraction around the way content is referred to in the system

## Overview

Abstracts the process of reading and writing to files behind ContentReader and
ContentWriter objects, tied together by a ContentService that simplifies the
creation and management of these objects. 

Content items are then referred to by their ContentId, which is a string
that represents the type of storage and a way of retrieving the content from
that storage location, eg 

    file:||content/folder/342/1233e342/filename.txt

Included is a Filesystem based implementation of ContentReader/Writer pair, 
which stores content on the filesystem in a content/ directory. 




See https://groups.google.com/forum/?hl=en&fromgroups=#!topic/silverstripe-dev/Z7CmioND5Ow 
for some further discussion of what's going on.


## Requirements

* SilverStripe 3.0
* [MultiValueField](https://github.com/nyeholt/silverstripe-multivaluefield)

