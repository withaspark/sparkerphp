#!/bin/bash
#
# CONFIGURES THE SPARKERPHP INSTALL
#



#
# Config
#

# Webserver group
WS_GRP='www-data'

# Uploads directory
UP_DIR='./uploads'


#
# Header
#
echo ""
echo "                      ---------------------------------"
echo "                       S P A R K E R P H P   S E T U P"
echo "                      ---------------------------------"
echo ""
echo "The MIT License (MIT)"
echo ""
echo "Copyright (c) 2009 Stephen Parker (http://withaspark.com)"
echo ""
echo "Permission is hereby granted, free of charge, to any person obtaining a copy"
echo "of this software and associated documentation files (the "Software"), to deal"
echo "in the Software without restriction, including without limitation the rights"
echo "to use, copy, modify, merge, publish, distribute, sublicense, and/or sell"
echo "copies of the Software, and to permit persons to whom the Software is"
echo "furnished to do so, subject to the following conditions:"
echo ""
echo "The above copyright notice and this permission notice shall be included in all"
echo "copies or substantial portions of the Software."
echo ""
echo "THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR"
echo "IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,"
echo "FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE"
echo "AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER"
echo "LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,"
echo "OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE"
echo "SOFTWARE."
echo ""
echo "--------------------------------------------------------------------------------"



#
# Must be root
#
echo ""
if [[ `whoami` != "root" ]]; then
	echo "   Error: Must be root to configure SparkerPHP automatically."
	echo ""
	exit 1
fi



#
# Install required software
#
# TODO:
echo "Installing required software and packages ..."

#
# Upload dir must be writable
#
if [[ ! -d "$UP_DIR" ]]; then
	echo "Creating $UP_DIR directory ..."
	mkdir -p "$UP_DIR"
fi
echo "Making $UP_DIR directory writable by application ..."
sudo chgrp "$WS_GRP" "$UP_DIR"
sudo chmod g+rw "$UP_DIR"

# Database
# Do this manually



#
# Footer
#
echo ""
echo "Setup complete!"
echo ""
exit 0
