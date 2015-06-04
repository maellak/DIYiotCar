#include <Wire.h>
#include <Adafruit_MotorShield.h>


// Setting up Motors on Adafruit Motorshield
// Left Motor is Motor 4 and Right Motor is Motor 3
Adafruit_MotorShield AFMS = Adafruit_MotorShield();
Adafruit_DCMotor *myMotor = AFMS.getMotor(3);
Adafruit_DCMotor *myMotor2 = AFMS.getMotor(4);

int motorspeedRight = 255;
int motorspeedLeft = 252;

//#define MOTORSPEED 255 //255 is maximum speed
void setup() {
  Serial.begin(9600); // set up Serial library at 9600 bps
AFMS.begin();
  myMotor->setSpeed(0);
  myMotor->run(FORWARD);
  // turn on motor
  myMotor->run(RELEASE);
  myMotor2->setSpeed(0);
  myMotor2->run(FORWARD);
  // turn on motor
  myMotor2->run(RELEASE);
}


void loop() {
  uint8_t i;
  if(Serial.available() > 0){
      
    //char test = Serial.read();
    //Serial.println(test);

    String directionsData = Serial.readString();
    Serial.println(directionsData);
    
    //String directions = directionsData.substring(0,1);
    //Serial.println(directionsData.substring(1,4).toInt());
    //Serial.println(directionsData.substring(5,8));

    if(directionsData.startsWith("w")){
      Serial.println("Forward ");
      
      if(directionsData.length() > 1){
        Serial.println("motorspeed");
      motorspeedRight = directionsData.substring(1,4).toInt();
      motorspeedLeft = directionsData.substring(5,8).toInt() - 3;
      }
      
      moveForward(myMotor, myMotor2);
    } 
    else if (directionsData.startsWith("s")){
      Serial.println("Backward ");
      moveBackward(myMotor, myMotor2);
    } 
    else if (directionsData.startsWith("a")){
      Serial.println("Left");
      moveLeft(myMotor, myMotor2);
    } 
    else if (directionsData.startsWith("d")){
      Serial.println("Right");
      moveRight(myMotor, myMotor2);
    }

    else if(directionsData.startsWith("q")){
      Serial.println("Stop");
      myMotor->run(RELEASE);
      myMotor2->run(RELEASE);
    }
  }
}

void moveForward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(FORWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveBackward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveRight(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(FORWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveLeft(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
