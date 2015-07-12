///////////////sonar parameters////////////
#include <OneWire.h>
#include <NewPing.h>
#include <math.h>
#define TRIGGER_PIN 12
#define ECHO_PIN 11
#define TRIGGER_PIN_LEFT 10
#define ECHO_PIN_LEFT 9
#define TRIGGER_PIN_RIGHT 8
#define ECHO_PIN_RIGHT 7
#define TRIGGER_PIN_DOWN 5
#define ECHO_PIN_DOWN 6
#define MAX_DISTANCE 200
NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE);
NewPing sonarLeft(TRIGGER_PIN_LEFT, ECHO_PIN_LEFT, MAX_DISTANCE);
NewPing sonarRight(TRIGGER_PIN_RIGHT, ECHO_PIN_RIGHT, MAX_DISTANCE);
NewPing sonarDown(TRIGGER_PIN_DOWN, ECHO_PIN_DOWN, MAX_DISTANCE);
float distance;
float distance_LEFT;
float distance_RIGHT;
float distance_DOWN;
float allDistances[]={0,0,0,0,0};
float allDistancesRight[]={0,0,0,0,0};
float allDistancesLeft[]={0,0,0,0,0};
float allDistancesDown[]={0,0,0,0,0};
/////////end sonar parameters///////////

/////encoder parameters///////////
int sensorPinLeft = 0,sensorPinRight = 1; //analog pin 0
int aktinaLeft=0,aktinaRight=0;
int prevActinaLeft=0,prevActinaRight=0;
int aktinesLeft=5,aktinesRight=5;
int strofesLeft=0,strofesRight=0;
int averageDistanceLeft=500,averageDistanceRight=500;
/////end encoder parameters///////////

void setup(){
  Serial.begin(9600);
}
////ΜΕΤΑΒΛΗΤΕΣ ΘΕΡΜΟΚΡΑΣΙΑΣ
  OneWire  ds(13);
int delayTemparature=0;////Η ΘΕΡΜΟΚΡΑΣΙΑ ΓΙΑ ΝΑ ΔΩΣΕΙ ΑΠΟΤΕΛΕΣΜΑΤΑ ΚΑΝΕΙ 750 ms
int delayAll=5;///ΤΟ DELAY ΠΟΥ ΔΙΝΟΥΜΕ ΓΙΑ ΤΟ ΟΛΙΚΟ SKETCH
float temperature=999;///ΕΔΩ ΘΑ ΔΙΝΕΤΑΙ Η ΘΕΡΜΟΚΡΑΣΙΑ, ΘΑ ΑΛΛΑΖΕΙ ΚΑΘΕ 1000 ms 
void loop(){
/////////////encoder///////////////////////////////////////////
//////////LEFT///////////////

  /////ΤΡΕΧΟΥΜΕ ΤΗΝ ΔΙΑΔΙΚΑΣΙΑ ΘΕΡΜΟΚΡΑΣΙΑΣ ΚΑΘΕ 1000 ms
  /////ΑΓΝΩΣΤΟ ΑΝ ΜΑΣ ΜΠΛΟΚΑΡΕΙ ΓΙΑ 750 ms 
  if (delayTemparature==0 )
  {
     
    //Serial.println("write Temp");
    writeTemp();
  }
  delayTemparature=delayTemparature+delayAll;
  if (delayTemparature==1000 )
  {
    //Serial.print("read Temp:");
    delayTemparature=0;
    temperature=getTemp();
    //Serial.println(temperature);
  }
  
  /////left wheeel//////////
  int valLeft = analogRead(sensorPinLeft);
  if ( valLeft<averageDistanceLeft && prevActinaLeft>averageDistanceLeft ) 
  {aktinaLeft=1;}
  else
  {aktinaLeft=0;}
  /*
  {aktinaLeft++;}
  if ( aktinesLeft==aktinaLeft ) 
  {
    aktinaLeft=0;
    strofesLeft++;
    Serial.print("|");
    Serial.println(strofesLeft);
  }
  */
  //Serial.println(val);
  prevActinaLeft=valLeft;
  
  
///////////////RIGHT wheel///////////////
  int valRight = analogRead(sensorPinRight);
  if ( valRight<averageDistanceRight && prevActinaRight>averageDistanceRight )  
  {aktinaRight=1;}
  else
  {aktinaRight=0;}
 // {aktinaRight++;}
  /*
  if ( aktinesRight==aktinaRight ) 
  {
    aktinaRight=0;
    strofesRight++;
    //Serial.print("|");
    //Serial.println(strofesRight);
  }
  */
  //Serial.println(val);
  prevActinaRight=valRight;
/////////////encoder end/////////////////////////////////

/////////////////////////////////////////////////////////////////
  //just to slow down the output - remove if trying to catch an object passing by
  delay(delayAll); 
  
  
  ////////////////////sonar start///////////////////////
  int uS = sonar.ping();
  int uS_LEFT = sonarLeft.ping();
  int uS_RIGHT = sonarRight.ping();
  int uS_DOWN = sonarDown.ping();
  float distance=uS / US_ROUNDTRIP_CM;
  float distance_LEFT=uS_LEFT / US_ROUNDTRIP_CM;
  float distance_RIGHT=uS_RIGHT / US_ROUNDTRIP_CM;
  float distance_DOWN=uS_DOWN / US_ROUNDTRIP_CM;
  int dist;
  int distLEFT;
  int distRIGHT;
  int distDOWN;
  
   dist = round(distance);
   distLEFT = round(distance_LEFT);
   distRIGHT = round(distance_RIGHT);
   distDOWN = round(distance_DOWN);
  
  ///////set protocol//////
  //////////@distance left*distance center*distance right*ακτινα bit left*ακτινα bit right#///////////////////////// 
  
  Serial.print("@");
  Serial.print(distLEFT);
  Serial.print("*");
  Serial.print(dist);
  Serial.print("*");
  Serial.print(distRIGHT);
  Serial.print("*");
  Serial.print(distDOWN);
  Serial.print("*");
  Serial.print(aktinaLeft);
  Serial.print("*");
  Serial.print(aktinaRight);
  Serial.print("*");
  Serial.print(temperature);
  Serial.println("#");

  
/////////////////////sonar end////////////////////////
}
  byte data[12];
  byte addr[8];
float writeTemp()
{
  byte i;
  byte present = 0;
  byte type_s;
  float celsius;
  
  if ( !ds.search(addr))
  {
    //Serial.println("No more addresses.");
    //Serial.println();
    ds.reset_search();
    return 999;
  }
  
 /* Serial.print("ROM =");
  for( i = 0; i < 8; i++) {
    Serial.write(' ');
    Serial.print(addr[i], HEX);
  }*/

  if (OneWire::crc8(addr, 7) != addr[7])
  {
      Serial.println("CRC is not valid!");
      return 999;
  }
  //Serial.println();
 
  // the first ROM byte indicates which chip
  switch (addr[0]) {
    case 0x10:
     // Serial.println("  Chip = DS18S20");  // or old DS1820
      type_s = 1;
      break;
    case 0x28:
   //   Serial.println("  Chip = DS18B20");
      type_s = 0;
      break;
    case 0x22:
 //     Serial.println("  Chip = DS1822");
      type_s = 0;
      break;
    default:
      //Serial.println("Device is not a DS18x20 family device.");
      return 999;
  } 

  ds.reset();
  ds.select(addr);
  ds.write(0x44, 1);        // start conversion, with parasite power on at the end
}
float getTemp()
{
  byte i;
  byte present = 0;
  byte type_s;
  float celsius;
  //delay(1000);     // maybe 750ms is enough, maybe not
  // we might do a ds.depower() here, but the reset will take care of it.
  
  present = ds.reset();
  ds.select(addr);    
  ds.write(0xBE);         // Read Scratchpad

 // Serial.print("  Data = ");
 // Serial.print(present, HEX);
  //Serial.print(" ");
  for ( i = 0; i < 9; i++) {           // we need 9 bytes
    data[i] = ds.read();
    //Serial.print(data[i], HEX);
   // Serial.print(" ");
  }
  //Serial.print(" CRC=");
  //Serial.print(OneWire::crc8(data, 8), HEX);
  //Serial.println();

  // Convert the data to actual temperature
  // because the result is a 16 bit signed integer, it should
  // be stored to an "int16_t" type, which is always 16 bits
  // even when compiled on a 32 bit processor.
  int16_t raw = (data[1] << 8) | data[0];
  if (type_s) {
    raw = raw << 3; // 9 bit resolution default
    if (data[7] == 0x10) {
      // "count remain" gives full 12 bit resolution
      raw = (raw & 0xFFF0) + 12 - data[6];
    }
  } else {
    byte cfg = (data[4] & 0x60);
    // at lower res, the low bits are undefined, so let's zero them
    if (cfg == 0x00) raw = raw & ~7;  // 9 bit resolution, 93.75 ms
    else if (cfg == 0x20) raw = raw & ~3; // 10 bit res, 187.5 ms
    else if (cfg == 0x40) raw = raw & ~1; // 11 bit res, 375 ms
    //// default is 12 bit resolution, 750 ms conversion time
  }
  celsius = (float)raw / 16.0;
  //fahrenheit = celsius * 1.8 + 32.0;
  //Serial.print("  Temperature = ");
  //Serial.print("Temperature is :");
  //Serial.print('@');
  //Serial.print(celsius);
  //Serial.println('#');
  return celsius;
  //Serial.print(" Celsius");
  //Serial.print(fahrenheit);
  //Serial.println(" Fahrenheit");
}
