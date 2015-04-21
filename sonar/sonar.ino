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
int dist;
int distLEFT;
int distRIGHT;


 dist = round(distance);
 dist_LEFT = round(distance_LEFT);
 dist_RIGHT = round(distance_RIGHT);

Serial.print("@");
Serial.print(dist_LEFT);
Serial.print("*");
Serial.print(dist);
Serial.print("*");
Serial.print(dist_RIGHT);
Serial.println("#");

}
