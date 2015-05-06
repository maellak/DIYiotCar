///////////////sonar parameters////////////
#include <NewPing.h>
#include <math.h>
#define TRIGGER_PIN 12
#define ECHO_PIN 11
#define TRIGGER_PIN_LEFT 10
#define ECHO_PIN_LEFT 9
#define TRIGGER_PIN_RIGHT 8
#define ECHO_PIN_RIGHT 7
#define MAX_DISTANCE 200
NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE);
NewPing sonarLeft(TRIGGER_PIN_LEFT, ECHO_PIN_LEFT, MAX_DISTANCE);
NewPing sonarRight(TRIGGER_PIN_RIGHT, ECHO_PIN_RIGHT, MAX_DISTANCE);
bool carIsRunning=true;
bool carBackwards=false;
int frenoDistance=15;
float distance;
float distance_LEFT;
float distance_RIGHT;
float allDistances[]={0,0,0,0,0};
float allDistancesRight[]={0,0,0,0,0};
float allDistancesLeft[]={0,0,0,0,0};
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

void loop(){
/////////////encoder///////////////////////////////////////////
//////////LEFT///////////////
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
  prevActina=valLeft;
  
  
///////////////RIGHT///////////////
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
  delay(5);
  
  
  
  ////////////////////sonar start///////////////////////
  int uS = sonar.ping();
  int uS_LEFT = sonarLeft.ping();
  int uS_RIGHT = sonarRight.ping();
  float distance=uS / US_ROUNDTRIP_CM;
  float distance_LEFT=uS_LEFT / US_ROUNDTRIP_CM;
  float distance_RIGHT=uS_RIGHT / US_ROUNDTRIP_CM;
  int dist;
  int distLEFT;
  int distRIGHT;
  
  
   dist = round(distance);
   distLEFT = round(distance_LEFT);
   distRIGHT = round(distance_RIGHT);
  
  ///////set protocol//////
  //////////@distance left*distance center*distance right*ακτινα bit left*ακτινα bit right#///////////////////////// 
  Serial.print("@");
  Serial.print(distLEFT);
  Serial.print("*");
  Serial.print(dist);
  Serial.print("*");
  Serial.print(distRIGHT);
  Serial.print("*");
  Serial.print(aktinaLeft);
  Serial.print("*");
  Serial.print(aktinaRight);
  Serial.println("#");
/////////////////////sonar end////////////////////////
  
  

}
