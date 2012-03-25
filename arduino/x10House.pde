/*
 X10 blink
 
 Blinks an lamp on an X10 lamp module.  
 Example was built using a PL513
 X10 One-Way Interface Module from http://www.smarthome.com
 as the modem, and a Powerhouse X10 Lamp Module from Smarthome
 to plug the lamp in.
 
 created 15 June 2007
 by Tom Igoe

*/
#include <x10.h>
#include <x10constants.h>

#define zcPin 9
#define dataPin 8


// set up a new x10 instance:
x10 myHouse =  x10(zcPin, dataPin);
int firstByte = 0;
int secondByte = 0;
int thirdByte = 0; 

int repeat = 1;

void setup()
{
  Serial.begin(9600);
}

void loop()
{
  
  // send data only when you receive data:
  if (Serial.available() > 0) 
  {
    // read the incoming byte:
    firstByte = Serial.read();

    if (Serial.available() > 0)
    {
      // read the incoming byte:
      secondByte = Serial.read();

      if (Serial.available() > 0)
      {
        // read the incoming byte:
        thirdByte = Serial.read();
      }
    }
  }

  byte letterByte = getX10Byte(firstByte);
  byte unitByte = getX10Byte(secondByte);
  byte instByte = getX10Byte(thirdByte);


  if(instByte == DIM)
  {
    myHouse.write(letterByte, unitByte, repeat);
    myHouse.write(letterByte, instByte, 10);
  }
  else if(instByte == BRIGHT)
  {
    myHouse.write(letterByte, unitByte, repeat);
    myHouse.write(letterByte, instByte, 18);
  }
  else
  {
    myHouse.write(letterByte, unitByte, repeat);
    myHouse.write(letterByte, instByte, repeat);
  }

  firstByte = 0;
  secondByte = 0;
  thirdByte = 0;
}  



byte getX10Byte(int x)
{
  byte result;

  switch (x)
  {
    case 97:
      result = A;
      break;
    case 98:
      result = B;
      break;
    case 99: 
      result = C;
      break;
    case 100:
      result = D;
      break;
    case 101:
      result = E;
      break;
    case 102:
      result = F;
      break;
    case 103:
      result = G;
      break;
    case 104:
      result = H;
      break;
    case 105:
      result = I;
      break;
    case 106:
      result = J;
      break;
    case 107:
      result = K;
      break;
    case 108:
      result = L;
      break;
    case 109:
      result = M;
      break;
    case 110:
      result = N;
      break;
    case 111:
      result = O;
      break;
    case 112:
      result = P;
      break;
    case 113:
      result = ON;
      break;
    case 114:
      result = OFF;
      break;
    case 115:
      result = DIM;
      break;
    case 116:
      result = BRIGHT;
      break;	

    case 65:
      result = UNIT_1;
      break;
    case 66:
      result = UNIT_2;
      break;
    case 67:
      result = UNIT_3;
      break;
    case 68:
      result = UNIT_4;
      break;
    case 69:
      result = UNIT_5;
      break;
    case 70:
      result = UNIT_6;
      break;
    case 71:
      result = UNIT_7;
      break;
    case 72:
      result = UNIT_8;
      break;
    case 73:
      result = UNIT_9;
      break;
    case 74:
      result = UNIT_10;
      break;
    case 75:
      result = UNIT_11;
      break;
    case 76:
      result = UNIT_12;
      break;
    case 78:
      result = UNIT_13;
      break;
    case 79:
      result = UNIT_14;
      break;
    case 80:
      result = UNIT_15;
      break;
    case 81:
      result = UNIT_16;
      break;
    default:
      result = A;
      break; 
  }

  return result;
}
