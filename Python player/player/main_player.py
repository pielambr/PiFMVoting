'''
Created on 10-mrt.-2014

@author: Pieterjan Lambrecht 
'''

from library import Library
from fm_player import FMPlayer

class MainPlayer:
    library = None
    player = None
    
    def __init__(self):        
        ' Set the frequency of the player '
        self.library = Library()
        self.library.scan()        
        ' Start the player '
        self.player = FMPlayer()
        
    def play(self):
        ' Check if there were any votes on the site... '
        if self.library.hasVotes():
            ' Play the next top voted song... '
            self.player.play(self.library.getTopVoted())
            self.play()            
        else:
            ' ... else just play some random song... '
            self.player.play(self.library.getRandomSong())
            self.play()