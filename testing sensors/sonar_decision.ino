#include <NewPing.h>

#define TRIGGER_PIN  12
#define ECHO_PIN     11
#define TRIGGER_PIN_LEFT  10
#define ECHO_PIN_LEFT     9
#define TRIGGER_PIN_RIGHT  8
#define ECHO_PIN_RIGHT     7
#define MAX_DISTANCE 200


NewPing sonar(TRIGGER_PIN, ECHO_PIN, MAX_DISTANCE);
NewPing sonarLeft(TRIGGER_PIN_LEFT, ECHO_PIN_LEFT, MAX_DISTANCE);
NewPing sonarRight(TRIGGER_PIN_RIGHT, ECHO_PIN_RIGHT, MAX_DISTANCE);

void setup() {
  Serial.begin(115200);
}

bool carIsRunning=true;
bool carBackwards=false;
int frenoDistance=15;
float distance;
float distance_LEFT;
float distance_RIGHT;
float allDistances[]={0,0,0,0,0};
float allDistancesRight[]={0,0,0,0,0};
float allDistancesLeft[]={0,0,0,0,0};

void loop() {
  delay(10);
  int uS = sonar.ping();
  int uS_LEFT = sonarLeft.ping();
  int uS_RIGHT = sonarRight.ping();
  float distance=uS / US_ROUNDTRIP_CM;
  float distance_LEFT=uS_LEFT / US_ROUNDTRIP_CM;
  float distance_RIGHT=uS_RIGHT / US_ROUNDTRIP_CM;
  //if (distance > frenoDistance && stopCar)
  if (distance > frenoDistance && !carBackwards)
  {
    findObject( distance );
    if ( !carIsRunning ) runCar( );
  }
  //else if ( distance<=10 && !stopCar)
  /////υπαρχει αντικειμενο 
  else if ( distance<=frenoDistance && distance!=0 || carBackwards )
  {
     if ( carIsRunning )
     {
         /////το οχημα πρεπει να σταματησει
       stopCar( distance );
     }
     if ( distance_LEFT>=distance_RIGHT && distance_LEFT>=frenoDistance )
     {
         ////το οχημα πρρεπει να κινηθει αριστερα        
         carBackwards=false;
         turnLEFT();
     }
     else if ( distance_LEFT<distance_RIGHT && distance_RIGHT>=frenoDistance )
     {
         ////το οχημα πρρεπει να κινηθει δεξια       
         carBackwards=false;
         turnRIGHT();
     }
     else 
     {
         ////το οχημα πρρεπει να κινηθει πισω         
        carBackwards=true;
        Serial.println("B");         
     }
  }
  else if( distance==0 )
  {
      if (! carIsRunning ) runCar( );
      noObject( ); 
  }
}
void runCar()
{
    carIsRunning=true;
    Serial.println("R");
}
void stopCar( float dist )
{
    findObject( dist );
    carIsRunning=false;
    Serial.println("S");
}
void noObject()
{
    Serial.println(""); 
}
void findObject( float dist )
{
  //dist=int( dist*10 )/10;
  int i;
  for(i=0;i<=int(dist);i++)
  {
    Serial.print(" ");    
  }
  Serial.println("|");
}
void turnLEFT()
{
    Serial.println("L");
}
void turnRIGHT()
{
    Serial.println("R");
}
float isDistanceValid( float distance, float alldistance[],float averageValue )
{
  float lastValue=alldistance[0];
  float sum=0;
  for(int i=0;i<sizeof(alldistance);i++)
  {
    sum+=alldistance[i];
  }
  float average=sum / sizeof(alldistance);
  for(int i=1;i<sizeof(alldistance);i++ )
  {
    alldistance[i]=alldistance[i-1];
  }
  if ( distance < average+averageValue && distance > average-averageValue ) { alldistance[0]=distance; } else { alldistance[0]=lastValue; }
  return alldistance[0];
  
}
