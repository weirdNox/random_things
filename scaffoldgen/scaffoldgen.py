#!/usr/bin/env python
# ====================================================================================================
# NOTE: Configuration
# All lengths in millimeters
WallWidth = 350e-3;
TrenchWidth = 500e-3;

LayerHeight = 140e-3;

LineCount = [15, 15];
LayerCount = 20;

# NOTE: 6 wells parameters
# WellStructure = [3, 2];
# LowerLeftWellCenterXY = [-40, -19.5];
# WellXYDiff = [39, 39];
# WellZOffset = 11;

# NOTE: 12 wells parameters
WellStructure = [4, 3];
LowerLeftWellCenterXY = [-36.2, -27.0];
WellXYDiff = [24.50, 24.50];
WellZOffset = 11;

SafeHeight = 30;

DualExtrusion = True;
SecondaryExtraHeight = 5;
SecondaryExtrusionMillis = 4000;

PrintSpeed = 300;
TravelSpeed = 4800;
ExtrusionParameter = True;

# ====================================================================================================
# NOTE: Generation
import math

LastSpeed = 0;

def printMove(Instrs, P, Speed = None):
    global LastSpeed;
    Speed = Speed or PrintSpeed;
    Cmd = (f"G1 X{P[0]:.4f} Y{P[1]:.4f}" +
           (" E1" if ExtrusionParameter else "") +
           (f" F{Speed}" if Speed != LastSpeed else ""));
    Instrs.append(Cmd);
    LastSpeed = Speed;

def travelMove(Instrs, P, Speed = None):
    global LastSpeed;
    Speed = Speed or TravelSpeed;
    Cmd = (f"G0 X{P[0]:.4f} Y{P[1]:.4f}" +
           (f" F{Speed}" if Speed != LastSpeed else ""));
    Instrs.append(Cmd);
    LastSpeed = Speed;

def travelZ(Instrs, NewZ, Speed = None):
    global LastSpeed;
    Speed = Speed or TravelSpeed;
    Cmd = (f"G0 Z{NewZ:.4f}" +
           (f" F{Speed}" if Speed != LastSpeed else ""));
    Instrs.append(Cmd);
    LastSpeed = Speed;

def getLayerHeight(OffsetZ, LayerIdx):
    return OffsetZ + (LayerIdx+1) * LayerHeight

def prepareExtruder(Instrs, Secondary):
    Instrs.append("M753"); # Deactivate both extruders
    if Secondary:
        Instrs.append("M712"); # Offset from 1 to 2
    else:
        Instrs.append("M721"); # Offset from 2 to 1

def activateExtruder(Instrs, Secondary):
    if Secondary:
        Instrs.append("M752"); # Set extruder 2 as active
    else:
        Instrs.append("M751"); # Set extruder 1 as active

def scaffold(Instrs, CenterXY, OffsetZ):
    travelZ(Instrs, SafeHeight);

    Direction = [1, 1];
    Pos = [CenterXY[0] + (-TotalWidth[0]/2 + WallWidth/2),
           CenterXY[1] + (-LineLength[0]/2 + WallWidth/2)];
    travelMove(Instrs, Pos);

    activateExtruder(Instrs, False); # Activate primary extruder

    for LayerIdx in range(LayerCount):
        CurrentHeight = getLayerHeight(OffsetZ, LayerIdx);
        travelZ(Instrs, CurrentHeight);

        if LayerIdx == 0:
            Instrs.append("M760"); # Start extruding primary

        LayerType = LayerIdx % 2;

        if LayerType == 0:
            # NOTE: Lines in the Y direction
            LineDir = 1;
            SideDir = 0;
        else:
            # NOTE: Lines in the X direction
            LineDir = 0;
            SideDir = 1;

        for LineIdx in range(LineCount[LayerType]):
            Pos[LineDir] += Direction[LineDir]*LineNozzleLength[LayerType];
            printMove(Instrs, Pos);
            Direction[LineDir] *= -1;

            if LineIdx < LineCount[LayerType]-1:
                Pos[SideDir] += Direction[SideDir]*LineWidth;
                printMove(Instrs, Pos);

        Direction[SideDir] *= -1;

    Instrs.append("M761"); # Stop extruding primary

LineWidth = WallWidth + TrenchWidth;
TotalWidth = [LineWidth*X - TrenchWidth for X in LineCount];
LineLength = TotalWidth[::-1];
LineNozzleLength = [X - WallWidth for X in LineLength];

Instrs = [];
Instrs.append(f"; Scaffold size: {TotalWidth}   (diagonal: {math.sqrt(TotalWidth[0]**2+TotalWidth[1]**2)})");
Instrs.append("G21"); # Set units to millimeters
Instrs.append("G90"); # Use absolute coordinates
Instrs.append("M83"); # Use relative distances for extrusion
Instrs.append("M753"); # Deactivate both extruders

for RowIdx in range(WellStructure[1]):
    OddRow = RowIdx % 2;
    for ColIdx in (range(WellStructure[0]-1, -1, -1) if OddRow else range(WellStructure[0])):
        WellCenterPos = [LowerLeftWellCenterXY[0] + ColIdx*WellXYDiff[0],
                         LowerLeftWellCenterXY[1] + RowIdx*WellXYDiff[1]];

        scaffold(Instrs, WellCenterPos, WellZOffset);

        if DualExtrusion:
            DropFromHeight = (getLayerHeight(WellZOffset, LayerCount-1) +
                              SecondaryExtraHeight);

            prepareExtruder(Instrs, True); # Prepare secondary extruder
            travelZ(Instrs, DropFromHeight);
            travelMove(Instrs, WellCenterPos);
            activateExtruder(Instrs, True); # Activate secondary extruder

            Instrs.append("M762"); # Start extruding secondary
            Instrs.append(f"G4 P{SecondaryExtrusionMillis:.4f}")
            Instrs.append("M763"); # Stop extruding secondary

            prepareExtruder(Instrs, False); # Prepare primary extruder
            # NOTE: The primary extruder is activated at the beginning of each scaffold

travelZ(Instrs, SafeHeight);

Instrs.append("G4"); # Wait for completion of the command queue

Code = "\n".join(Instrs);
print(Code);
