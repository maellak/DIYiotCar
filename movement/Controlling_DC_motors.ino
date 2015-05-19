#include <Wire.h>
#include <Adafruit_MotorShield.h>
#include <SerialHelper.h>
 
SerialHelper serialHelper(128, 0);
// Setting up Motors on Adafruit Motorshield
// Left Motor is Motor 4 and Right Motor is Motor 3
Adafruit_MotorShield AFMS = Adafruit_MotorShield();
Adafruit_DCMotor *myMotor = AFMS.getMotor(3);
Adafruit_DCMotor *myMotor2 = AFMS.getMotor(4);

int leftSpeed = 0;
int rightSpeed = 0;

void setup() {
  Serial.begin(9600); // set up Serial library at 9600 bps
  AFMS.begin();
  myMotor->setSpeed(leftSpeed);
  myMotor->run(FORWARD);
  // turn on motor
  myMotor->run(RELEASE);
  myMotor2->setSpeed(rightSpeed);
  myMotor2->run(FORWARD);
  // turn on motor
  myMotor2->run(RELEASE);
}
void loop() {
  uint8_t i;



  if(Serial.available() > 0){
    
    //leftSpeed = serialHelper.read();
    //rightSpeed = Serial.read();
    
    serialHelper.read();
    char *test = serialHelper.getMessage();
    int len = serialHelper.getMessageLength();
    //Serial.println();
    //Serial.println(test);
    if(strcmp(test, "w") == 0){
       serialHelper.write(test, len);
      moveForward(myMotor, myMotor2);
    }
    else if (strcmp(test, "s") == 0){
       serialHelper.write(test, len);
      moveBackward(myMotor, myMotor2);
    }
    else if (strcmp(test, "a") == 0){
       serialHelper.write(test, len);
      moveLeft(myMotor, myMotor2);
    }
    else if (strcmp(test, "d") == 0){
       serialHelper.write(test, len);
      moveRight(myMotor, myMotor2);
    }
    else if(strcmp(test, "q") == 0){
       serialHelper.write(test, len);
      myMotor->run(RELEASE);
      myMotor2->run(RELEASE);
    }
  }
}


void invert(char *buff, int len) {
  int i, tmp;

  for( i = 0 ; i < len / 2 ; i++ ) {
      tmp = buff[len - i - 1];
      buff[len - i - 1] = buff[i];
      buff[i] = tmp;
    }
}
void moveForward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(FORWARD);
  motor->setSpeed(leftSpeed);
  motor2->setSpeed(rightSpeed);
}
void moveBackward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(leftSpeed);
  motor2->setSpeed(rightSpeed);
}
void moveRight(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(BACKWARD);
  motor2->run(FORWARD);
  motor->setSpeed(leftSpeed);
  motor2->setSpeed(rightSpeed);
}
void moveLeft(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  uint8_t i;
  motor->run(FORWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(leftSpeed);
  motor2->setSpeed(rightSpeed);
}
