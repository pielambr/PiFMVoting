'''
Created on 10-mrt.-2014

@author: Pieterjan Lambrecht
'''
import sqlite3, os, ConfigParser
from mutagen.mp3 import MP3
from mutagen.easyid3 import EasyID3
from api import API

class Library:
    musicpath = None
    connection = None
    api = None
    
    def __init__(self):
        print("Initializing library...")
        parser = ConfigParser.ConfigParser()
        parser.read("pifm_cfg.config")
        self.musicpath = parser.get("PiFM", "musicdir")
        self.api = API()
        self.connection = sqlite3.connect("pifm_music.db")
        self.checkIfDBExists()
        
    def insertIntoDB(self, songlist):
        ' Insert these songs into the database '
        for song in songlist:
            audio = MP3(song)
            id3 = EasyID3(song)
            ' Check artist, title and album for this song '
            try:
                artist = str(id3['artist'][0])
            except KeyError:
                artist = None
            try:
                title = str(id3['title'][0])
            except KeyError:
                title = None
            try:
                album = str(id3['album'][0])
            except KeyError:
                album = None
            ' Check length and bitrate of the song '
            length = audio.info.length
            bitrate = audio.info.bitrate
            self.insertSong(artist, title, album, song, length, bitrate)   
    
    def scan(self):
        ' Make a list with all the audio files in a directory '
        file_list = []
        for root, folders, files in os.walk(self.musicpath):
            folders.sort()
            files.sort()
            for filename in files:
                if filename.lower().endswith(".mp3"): 
                    file_list.append(os.path.join(root, filename))
        self.insertIntoDB(file_list)
    
    def insertSong(self, artist, title, album, path, length, quality):
        ' Insert song into the database if needed '
        if artist != "" and title != "" and artist != None and title != None:
            if not self.songExists(artist, title):
                ' This song was not in our database yet ' 
                c = self.connection.cursor()
                c.execute("INSERT INTO PiSongs (artist, title, album, path, last_played, length, quality) VALUES (?,?,?,?,?,?,?)", (self.clean(artist), self.clean(title), self.clean(album), self.clean(path), 0, length, quality))
                self.connection.commit()
                self.api.insertSong(artist, title)
            else:
                ' We know the song exists, but does this version have a better quality? '
                c = self.connection.cursor()
                c.execute("SELECT quality FROM PiSongs WHERE title = ? AND artist = ?", (self.clean(title), self.clean(artist)))
                rate = c.fetchone()
                if rate[0] < quality:
                    c.execute("DELETE FROM PiSongs WHERE title = ? AND artist = ?", (self.clean(title), self.clean(artist)))
                    self.connection.commit()
                    c = self.connection.cursor()
                    c.execute("INSERT INTO PiSongs (artist, title, album, path, last_played, length, quality) VALUES (?,?,?,?,?,?,?)", (self.clean(artist), self.clean(title), self.clean(album), self.clean(path), 0, length, quality))
                    self.connection.commit()
        else:
            ' Too little tags to add this to our database '
            print("Missing tags for : " + path)
        
    def checkIfDBExists(self):
        ' Check if database exists, if not, create it '
        c = self.connection.cursor()
        c.execute('CREATE TABLE IF NOT EXISTS PiSongs (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, artist TEXT, album TEXT, path TEXT, last_played NUMERIC, length NUMERIC, quality NUMERIC);')
        self.connection.commit()
        
    def clean(self, n):
        ' Make a nice string so sqlite does not have to complain '
        if n == None:
            return ""
        else:
            return unicode(n)
        
    def songExists(self, artist, title):
        ' Check if a song with this artist and title already exists '
        c = self.connection.cursor()
        c.execute("SELECT * FROM PiSongs WHERE artist = ? AND title = ?", (self.clean(artist), self.clean(title)))
        results = c.fetchall()
        if len(results) > 0:
            return True
        return False    
    
    def hasVotes(self):
        return self.api.hasVotes()
        
    def getTopVoted(self):
        a = self.api.popVote()
        artist = a[1]
        title = a[2]
        song = self.getSong(artist, title)
        return song[4]
        
    def getSong(self, artist, title):
        c = self.connection.cursor()
        c.execute("SELECT * FROM PiSongs WHERE title = ? AND artist = ?", (self.clean(title), self.clean(artist)))
        r = c.fetchone()
        return r
        
    def getRandomSong(self):  
        ' Play a random song, that has not been played in a while '
        c = self.connection.cursor()
        c.execute("SELECT * FROM PiSongs ORDER BY RANDOM() LIMIT 1")
        result = c.fetchone()
        return result[4]