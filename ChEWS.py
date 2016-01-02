#!/usr/bin/python
# -*- coding: utf-8 -*-
#
#############################################################################
#
# Copyright Graham Jones, 2015
#
#############################################################################
#
#   This file is part of ChEWS.
#
#    ChEWS is free software: you can redistribute it and/or modify
#    it under the terms of the GNU General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    ChEWS is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU General Public License for more details.
#
#    You should have received a copy of the GNU General Public License
#    along with ChEWS.  If not, see <http://www.gnu.org/licenses/>.
##############################################################################
#

"""
ChEWS - Chemical Element Word Solver - spell names using chemical element
symbols.
"""
import json
import svgwrite
import os
import sys

__appname__ = "ChEWS"
__author__  = "Graham Jones"
__version__ = "0.1"
__license__ = "GNU GPL 3.0 or later"


class ChEWS:
    periodicTable = None
    elemArray = []
    maxElemNo = None
    debug = False
    
    #####################################################
    def __init__(self,debug=False):
        """
        Read the periodicTable.json file of element data into an array of
        element objects elemArray.
        :param debug: flag to switch on debugging output.
        """
        self.debug = debug
        if (self.debug): print "ChEWS.__init__()"
        scriptDir = os.path.dirname(os.path.realpath(__file__))
        if (self.debug): print scriptDir
        dataFname = os.path.join(scriptDir, "periodicTable.json")
        if (self.debug): print dataFname
        f = open(dataFname, 'r')
        elemJSON = f.read()
        # print elemJSON
        self.periodicTable = json.loads(elemJSON)
        if (self.debug): print "Loaded data for %d element groups from file" \
            % len(self.periodicTable['table'])

        for elemGroup in self.periodicTable['table']:
            for elemObj in elemGroup['elements']:
                self.elemArray.append(elemObj)
        for elemObj in self.periodicTable['lanthanoids']:
                self.elemArray.append(elemObj)
        for elemObj in self.periodicTable['actinoids']:
                self.elemArray.append(elemObj)        
                
        if (self.debug): print "Loaded data for %d elements " % len(self.elemArray)
        self.maxElemNo = len(self.elemArray)

    # ####################################################
    def showElements(self):
        """
        Write all elements in database to screen
        """
        for elemObj in self.elemArray:
            print "%d %s (%s)" % (elemObj['number'],elemObj['small'],elemObj['name'])

    #####################################################
    def elem2Str(self, elemList):
        """
        Convert a list of atomic numbers into a string of element
        symbols.
        """
        outString = ""
        if (elemList is None):
            print "elem2Str - empty list provided returning empty string"
            return outString
        
        for elemNo in elemList:
            found = False
            for elemObj in self.elemArray:
                if (elemObj['number'] == elemNo):
                    found = True
                    # print "adding ement %d - string is \"%s\"" \
                    #    % (elemNo,elemObj['small'])
                    outString = "%s%s" % (outString, elemObj['small'])
            if (not found):
                # print "ERROR - Can not Find Element Number %d" % elemNo
                outString = "%s%s" % (outString, "?")
        # print outString
        return outString


    #####################################################
    def isPartMatch(self,targetStr,curStr):
        """
        Checks to see if the characters in curStr match the
        start of targetStr.   Comparison is not case sensitive.
        Returns false if the curStr string is empty.
        """
        curLen = len(curStr)
        if curLen == 0:
            return False
        if (targetStr[:curLen].lower() == curStr.lower()):
            return True
        else:
            return False

    #####################################################
    def findNextMatch(self, targetStr, elemList):
        """
        Find the next set of elements that produce targetStr, starting
        with the current element list elemList.
        Returns a tuple (success, result).  Success is true if match found,
        or false if it fails.  Result is a list of the atomic numbers of the
        elements that spell the target string.
        """
        if (self.debug): print "findNextMatch()"
        # If we are not provided with a starting list of elements, create one.
        if len(elemList) == 0:
            if (self.debug): print "empty resut list provided - creating one"
            elemList.append(-1)
        #
        # Loop forever
        while (True):
            # Increment the last element in the element list array.
            lastElemPos = len(elemList)-1
            elemList[lastElemPos] = elemList[lastElemPos] + 1
            #
            # If we have got to the end of available elements and not found a
            # match, we have to go backwards and change the preceding symbol.
            # in the element list to try to find a solution.
            if (elemList[lastElemPos] > self.maxElemNo):
                if (self.debug):
                    print "got to end of element list - "
                    + "removing last element and trying again"
                # Trim of last element of elemList
                elemList = elemList[:lastElemPos]
                if (self.debug): print "elemList = "
                if (self.debug): print elemList
                # if we have removed the first element of the array, we have
                # failed to find a solution at all.
                if (len(elemList) <= 0):
                    print "   Oh no - failed completely"
                    return (False, elemList)
            else:
                if (self.debug):
                    print "current String is %s" \
                        % self.elem2Str(elemList)
                    print "elemList = "
                    print elemList
                # Check to see if we have found the solution
                if (self.elem2Str(elemList).lower() == targetStr.lower()):
                    if (self.debug):
                        print "**** Solution Found for %s ****" % targetStr
                        print elemList
                    return (True, elemList)
                elif (self.isPartMatch(targetStr, self.elem2Str(elemList))):
                    if (self.debug): print "Partial Match Found - adding another element"
                    elemList.append(0)
            

    #####################################################

    def getElemObj(self,elemNo):
        """
        Return the python object representing element with atomic number
        elemNo.  Returns None if elemNo is not found in the database.
        """
        for elemObj in self.elemArray:
            if (elemObj['number'] == elemNo):
                return elemObj
        return None

    def svgrender(self, elemObj, xpos=0, boxX=120, boxY=130):
        """
        Render an element elemObj into an SVG file and return the svgwrite
        Drawing object.   xpos is the positon of the objects in the drawing.
        boxX and boxY are the size of the drawing.
        """
        fontSize = 70
        smallFontSize = fontSize/5

        # No, I don't know why the viewbox offset is -xpos rather than xpos
        # either, but it has to be negative for them to appear in the right
        # place!
        dwg = svgwrite.Drawing(None,
                               size=(toPt(boxX), toPt(boxY)),
                               viewBox=("%d %d %d %d" %
                                        (offset((0, 0), -xpos, 0) +
                                         offset((boxX, boxY), 0, 0))))
        dwg.add(dwg.rect((0, 0), (boxX, boxY),
                         stroke='black',
                         fill='white'))

        textOrigin = (5, smallFontSize+fontSize)

        # Main Symbol
        textElem = dwg.text("", insert=offset(textOrigin, smallFontSize/2, 0))
        textElem.add(dwg.tspan(elemObj['small'],
                               font_size="%dpt" % fontSize,
                               fill='black'))
        dwg.add(textElem)

        # Atomic Weight
        textElem = dwg.text("", insert=offset(textOrigin, 0, -1*fontSize))
        textElem.add(dwg.tspan("%5.1f" % elemObj['molar'],
                               font_size=toPt(smallFontSize),
                               fill='black'))
        dwg.add(textElem)

        # Atomic Number
        textElem = dwg.text("",
                            insert=offset(textOrigin, 0, +1.5*smallFontSize))
        textElem.add(dwg.tspan("%2d" % elemObj['number'],
                               font_size=toPt(smallFontSize),
                               fill='black'))
        dwg.add(textElem)

        # Name
        textElem = dwg.text("", insert=offset(textOrigin, 0, +smallFontSize*3))
        textElem.add(dwg.tspan(elemObj['name'],
                               font_size=toPt(smallFontSize),
                               fill='black'))
        dwg.add(textElem)
        # dwg.save()
        return dwg

    def renderAll(self, elemList, fname="test.svg"):
        """ Render all of the elements in elemList to an SVG file.
        Returns a svgwrite Drawing object.
        """
        boxX = 120
        boxY = 130
        dwg = svgwrite.Drawing(fname,
                               size=(toPt(boxX*len(elemList)), toPt(boxY)),
                               viewBox=("0 0 %d %d" %
                                        (boxX*len(elemList), boxY)))
        n = 0
        for elemNo in elemList:
            elemObj = self.getElemObj(elemNo)
            elemDwg = self.svgrender(elemObj, n*boxX, boxX, boxY)
            dwg.add(elemDwg)
            n = n + 1

        if (fname is not None):
            dwg.save()
        return dwg


def offset(pos, dx, dy): return (pos[0]+dx, pos[1]+dy)


def toPt(val): return "%dpt" % val


"""
Main Program - reads command line arguments and attempts to solve using
the ChEWS class
"""
if (__name__ == "__main__"):
    from optparse import OptionParser
    parser = OptionParser(version="%%prog v%s" % __version__,
                          usage="%prog [options] <argument> ...",
                          description=__doc__.replace('\r\n', '\n')
                          .split('\n--snip--\n')[0])
    parser.add_option('-s', '--svg', dest="svg",
                      help="Write an svg image of the solution to "
                      "the specified file.")
    parser.add_option('-p', '--png', dest="png",
                      help="Write a PNG image of the solution to "
                      "the specified file.")

    opts, args = parser.parse_args()

    # print opts
    # print dir(opts)
    # print args

    if (len(args) < 1):
        parser.print_help()
        sys.exit(1)
    else:
        print "ChEWS - Chemical Element Word Solver"
        targetStr = args[0]
        chews = ChEWS(False)
        # chews.showElements()
        # print chews.elem2Str((1,57,1))
        # print chews.isPartMatch("hello","hel")
        # print chews.isPartMatch("hello","hello")
        # print chews.isPartMatch("hello","helo")

        (success, elemList) = chews.findNextMatch(targetStr, [])
        if (success):
            print "found solution - target = %s answer = %s" \
                % (targetStr, chews.elem2Str(elemList))
            print elemList
            if opts.svg:
                print "producing SVG file %s." % opts.svg
                print chews.renderAll(elemList, fname=opts.svg)
            if opts.png:
                print "producing PNG file %s." % opts.png
                svgStr = chews.renderAll(elemList, fname="tmp.svg").tostring()

                # cairosvg.svg2png(url="tmp.svg",write_to=opts.png)
            sys.exit(0)
        else:
            print "Failed to Find Solution for %s" % targetStr
            sys.exit(1)
