import sys
import json

# AU to KM
AUKM = 149597870.691

Data = []

# simple dictionary to store distances per object
# note: if we have multiple instances per object
# we keep the latest one
distance = {}

# Read distance per object 
# http://www.minorplanetcenter.net/iau/lists/CloseApp.html
clos = open(sys.argv[2], 'r')
ignore = True
for line in clos:
	if ignore :
		ignore = False
		continue
	try:
		name = line[10-1:20].rstrip()		
		dist = line[58-1:68].rstrip()
		dist = float(dist)*AUKM
		if dist > 1000000 :
			continue
		distance[name] = dist		
	except ValueError:
		pass

# Now read Near Earth Object data
# http://www.minorplanetcenter.net/iau/MPCORB/NEA.txt
neo = open(sys.argv[1], 'r')
for line in neo :
	try:
		des = line[166+9:194].rstrip()
		H = line[8:13].rstrip()
		H = float(H)
		# we assume that Albedo = 0.15
		# http://www.physics.sfasu.edu/astro/asteroids/sizemagnitude.html
		pl = 0.05
		ph = 0.25
		Hl = (1329 / (pl**.5)) * (10**(-0.2*H))*1000
		Hh = (1329 / (ph**.5)) * (10**(-0.2*H))*1000
		if Hl < 4 :
			continue
		if des in distance :
			Data.append({'object':des, 'H':[Hl,Hh], 'distance':distance[des]})		
	except KeyError:
		continue

print json.dumps(Data)