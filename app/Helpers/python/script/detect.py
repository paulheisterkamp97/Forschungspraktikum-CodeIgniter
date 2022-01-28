#!/usr/bin/env python
import argparse
import numpy as np
import time
import cv2
import json
import os
from PIL import Image
import pymysql.cursors


confthres = 0.5
# Non maximum Suppression
nmsthres = 0.3

config_file = os.path.join('..','config','yolov3-spp-custom-9c.cfg')
names_file = os.path.join('..','config','obj.names')
weights_file  = os.path.join('..','config','yolov3-spp-custom-9c_best.weights')
uploads_dir = os.path.join('..','..','..','..','writable','uploads')

def load_model(config_path, weights_path):
    print("[INFO] loading YOLO from Darknet model...")
    netz = cv2.dnn.readNetFromDarknet(config_path, weights_path)
    return netz

def read_data(request, toread):
    file_name = ""
    if toread in request.args:
        file_name = request.args.get(toread)
    if toread in request.form:
        file_name = request.form[toread]
    return file_name

def get_prediction(image, netz):
    # Layer ubutuakusuereb
    ln = netz.getLayerNames()
    ln = [ln[i - 1] for i in netz.getUnconnectedOutLayers()]

    # Bild fuer die Objekterkennung herunterskalieren
    blob = cv2.dnn.blobFromImage(image, 1 / 255.0, (608, 608),
                                 swapRB=True, crop=False)
    netz.setInput(blob)
    # Zeit fuer die Laufzeitanalyse messen
    start = time.time()
    layerOutputs = netz.forward(ln)
    print(layerOutputs)
    end = time.time()

    # Ausgabe fuer Laufzeitanalyse
    print("[INFO] YOLO took {:.6f} seconds".format(end - start))
    # Initialisierung
    boxes = []
    confidences = []
    classIDs = []

    for output in layerOutputs:
        for objects in output:
            scores = objects[5:]
            classID = np.argmax(scores)
            confidence = scores[classID]

            # Schwache Vorhersagen ignorieren
            if confidence > confthres:
                # Box skalieren
                box = objects[0:4] #* np.array([W, H, W, H])
                (centerX, centerY, width, height) = box

                # vom Mittelpunkt ausgehend
                x = centerX - (width / 2)
                y = centerY - (height / 2)

                # Box der Ausgabe hinzufuegen
                boxes.append([x, y, width, height])
                confidences.append(float(confidence))
                classIDs.append(classID)

    # ueberlappungen vermeiden
    idxs = cv2.dnn.NMSBoxes(boxes, confidences, confthres,
                            nmsthres)
    out = []
    # Wenn mindestens eine Box existiert
    if len(idxs) > 0:
        for i in idxs.flatten():
            box = {}
            box["class"] = int(classIDs[i])
            box["x"] = float(boxes[i][0])
            box["y"] = float(boxes[i][1])
            box["w"] = float(boxes[i][2])
            box["h"] = float(boxes[i][3])
            out.append(box)
    return out

def parse_args():
    parser = argparse.ArgumentParser(description='parser for detection script')
    parser.add_argument('imageId',type=int,default=1)
    return parser.parse_args()
# Press the green button in the gutter to run the script.

if __name__ == '__main__':
    args = parse_args()
    cnx = pymysql.connect(user='ci_connect_w',
                                     host='sql643.your-server.de',
                                     database='object_detection',
                                     password='cT5Bs6HUtGv94M6Z')
    cursor = cnx.cursor()
    query  = ("SELECT path FROM pictures WHERE id="+str(args.imageId))

    image_path = ""
    cursor.execute(query)
    for (path) in cursor:
        image_path , = path



    netz = load_model(config_file, weights_file)
    image_file = Image.open(os.path.join(uploads_dir,image_path))
    # Bild generieren
    npimg = np.array(image_file)
    image = cv2.cvtColor(npimg, cv2.COLOR_BGR2RGB)
    out = get_prediction(image, netz)

    with open(os.path.join(uploads_dir,'out',"test,json"),"w+") as f:
        f.write(json.dumps(out))


    add_hitboxes = ("INSERT INTO hitboxes "
                   "(id, class, x, y, w , h) "
                   "VALUES (%(id)s, %(class)s, %(x)s, %(y)s, %(w)s, %(h)s)")
    if not out:
        box = {}
        box["class"] =0
        box["x"] = 0
        box["y"] = 0
        box["w"] = 0
        box["h"] = 0
        out.append(box)

    for row in out:
        addrow = row
        addrow['id'] = args.imageId
        cursor.execute(add_hitboxes, addrow)

    cnx.commit()
    cursor.close()
    cnx.close()
