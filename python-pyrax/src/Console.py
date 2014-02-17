#!/usr/local/bin/python2.7
# encoding: utf-8

import sys
from twisted.python.util import println
from org.mbs3.pyrax.devops import Challenge6, Challenge8

def main(argv=None): # IGNORE:C0111
    '''Command line options.'''

    if argv is None:
        argv = sys.argv
    else:
        sys.argv.extend(argv)
        
    for a in argv:
        println(a)

    if(len(argv) <= 1):
        println("No arguments, exiting")
    elif(argv[1] == "challenge6"):
        c = Challenge6.Challenge6()
        c.challenge(argv[2:])
    elif(argv[1] == "challenge8"):
        c = Challenge8.Challenge8()
        c.challenge(argv[2:])
    else:
        println("I don't recognize your argument %s " % argv[0])
    
if __name__ == "__main__":
    sys.exit(main())