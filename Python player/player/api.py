'''
Created on 14-mrt.-2014

@author: Pieterjan Lambrecht
'''
import ConfigParser, requests, json
class API:
    
    apiurl = None
    user = None
    passwd = None
    
    def __init__(self):        
        parser = ConfigParser.ConfigParser()
        parser.read("pifm_cfg.config")
        self.apiurl = parser.get("API", "url")
        self.user = parser.get("API", "username")
        self.passwd = parser.get("API", "password")
        print("API initialised for " + self.apiurl)
        
    def deleteSong(self, artist, title):
        ' Contact API to delete certain song '
        parameters = {'user' : self.user, 'password': self.passwd, 'request' : 'songs', 'option' : 'delete', 'artist' : artist, 'title' : title}
        r = requests.post(self.apiurl, parameters)
        
    def insertSong(self, artist, title):
        ' Contact API to insert certain song '
        parameters = {'user' : self.user, 'password': self.passwd, 'request' : 'songs', 'option' : 'insert', 'artist' : artist, 'title' : title}
        r = requests.post(self.apiurl, parameters)
        
    def popVote(self):
        ' Pop the top-voted song ' 
        parameters = {'user' : self.user, 'password': self.passwd, 'request' : 'votes', 'option' : 'pop'}
        r = requests.post(self.apiurl, parameters)
        return json.loads(r.text)
        
    def getVotedSongs(self):
        ' Play the top voted and remove it from the list '
        parameters = {'user' : self.user, 'password': self.passwd, 'request' : 'votes', 'option' : 'list'}
        r = requests.post(self.apiurl, parameters)
        return json.loads(r.text)
        
    def hasVotes(self):
        ' Check if there are any votes '
        results = None
        results = self.getVotedSongs()
        return not(results == None or len(results) < 1)