#!/usr/bin/env python
'''
Created on 10-mrt.-2014

@author: Pieterjan Lambrecht 
'''
import ConfigParser, os, sys
import RPi.GPIO as GPIO
from player.main_player import MainPlayer

class PiPlayer:
    ' Global variables '
    player = None
        
    def __del__(self):
        ' Clean up the GPIO pins - no more broadcasting on PIN 4'
        GPIO.cleanup()

    def start(self):
        ' Startup the player '
        self.player = MainPlayer()
        self.player.play()
        
' Try making a new process, for streaming purposes '
fpid = os.fork();
if fpid != 0:
    sys.exit(0); 
    
' Start the PiFM player'
player = PiPlayer()
player.start()
        