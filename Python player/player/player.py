'''
Created on 10-mrt.-2014

@author: Pieterjan Lambrecht
'''
import os, subprocess;

class FMPlayer:
    ' Process used for FM streaming '
    fm_process = None;
    
    ' Pipes for reading and writing '
    music_pipe_r,music_pipe_w = os.pipe()
    
    def __init__(self, freq):        
        print("Starting PiFM Pi Player on " + str(freq) + " FM...");
        self.frequency = freq;        
                   
        ' Now a process for the FM streaming '            
        with open(os.devnull, "w") as dev_null:
            self.fm_process = subprocess.Popen(["/root/pifm","-",str(freq),"44100", "stereo"], stdin=self.music_pipe_r, stdout=dev_null);
            
    def play(self, song):        
        ' Play the song provided to this function '
        with open(os.devnull, "w") as dev_null:
            subprocess.call(["ffmpeg","-i",song,"-f","s16le","-acodec","pcm_s16le","-ac", "2", "-ar","44100","-"],stdout=self.music_pipe_w, stderr=dev_null);
            print("Playing song; " + song);
        