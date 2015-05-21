#include <Wire.h>
#include <Adafruit_MotorShield.h>


 // Setting up Motors on Adafruit Motorshield
 // Left Motor is Motor 4 and Right Motor is Motor 3
 Adafruit_MotorShield AFMS = Adafruit_MotorShield();
 Adafruit_DCMotor *myMotor = AFMS.getMotor(3);
 Adafruit_DCMotor *myMotor2 = AFMS.getMotor(4);

 #define MOTORSPEED 255 //255 is maximum speed
 void setup() {
   Serial.begin(9600); // set up Serial library at 9600 bps

void loop() {
   uint8_t i;
   if(Serial.available() > 0){
     char test = Serial.read();

   //Serial.println();
     //Serial.println(test);
     if(test == 'w'){

     //Serial.print("Forward ");
       moveForward(myMotor, myMotor2);
     } 
     else if (test == 's'){

    //Serial.print("Backward ");
       moveBackward(myMotor, myMotor2);
     } 
     else if (test == 'a'){

    //Serial.print("Left");
       moveLeft(myMotor, myMotor2);
     } 
     else if (test == 'd'){

    //Serial.print("Right");
       moveRight(myMotor, myMotor2);
     }

 else if(test == 'q'){
       myMotor->run(RELEASE);
       myMotor2->run(RELEASE);
     }
void moveLeft(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
   motor->setSpeed(MOTORSPEED);
   motor2->setSpeed(MOTORSPEED);
 }

void moveForward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(FORWARD);
  motor->setSpeed(MOTORSPEED);
  motor2->setSpeed(MOTORSPEED);
}
void moveBackward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(MOTORSPEED);
  motor2->setSpeed(MOTORSPEED);
}
void moveRight(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(FORWARD);
  motor->setSpeed(MOTORSPEED);
  motor2->setSpeed(MOTORSPEED);
}
void moveLeft(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(MOTORSPEED);
  motor2->setSpeed(MOTORSPEED);
}
