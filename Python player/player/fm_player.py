'''
Created on 10-mrt.-2014

@author: Pieterjan Lambrecht
'''
import os, subprocess, ConfigParser

class FMPlayer:
    ' Process used for FM streaming '
    fm_process = None
    
    ' Pipes for reading and writing '
    music_pipe_r,music_pipe_w = os.pipe()
    
    def __init__(self):             
        parser = ConfigParser.ConfigParser()
        parser.read("pifm_cfg.config")
        frequency = parser.get("PiFM", "frequency")  
        print("Starting PiFM Pi Player on " + str(frequency) + " FM...");      
                   
        ' Now a process for the FM streaming '            
        with open(os.devnull, "w") as dev_null:
            self.fm_process = subprocess.Popen(["/root/pifm","-",str(frequency),"44100", "stereo"], stdin=self.music_pipe_r, stdout=dev_null)
            
    def play(self, song):        
        ' Play the song provided to this function '
        with open(os.devnull, "w") as dev_null:
            print("Playing song; " + song)
            subprocess.call(["ffmpeg","-i",song,"-f","s16le","-acodec","pcm_s16le","-ac", "2", "-ar","44100","-"],stdout=self.music_pipe_w, stderr=dev_null)
        