#include <Wire.h>
#include <Adafruit_MotorShield.h>
#include <QueueArray.h>


// Setting up Motors on Adafruit Motorshield
// Left Motor is Motor 1 and Right Motor is Motor 2
Adafruit_MotorShield AFMS = Adafruit_MotorShield();
Adafruit_DCMotor *myMotor = AFMS.getMotor(1);
Adafruit_DCMotor *myMotor2 = AFMS.getMotor(2);

//#define MOTORSPEED 255 //252 is maximum speed
int motorspeedRight = 255;
int motorspeedLeft = 252;

void setup() {
  Serial.begin(9600); // set up Serial library at 9600 bps
  AFMS.begin();
  //in the beginng start/stop the motors
  myMotor->setSpeed(0);
  myMotor->run(FORWARD);
  // turn on motor
  myMotor->run(RELEASE);
  myMotor2->setSpeed(0);
  myMotor2->run(FORWARD);
  // turn on motor
  myMotor2->run(RELEASE);
  
  Serial.setTimeout(5);  // degrees timeout because it had to wait 2 minutes for every movement
}


   String command;
   int commandIndex;
   QueueArray <String> stack;
void loop() {
  String *allcomands;
  if(Serial.available() > 0){
       
    //read direction & speed from serial then print direction and/or speed
    String directionsData = Serial.readString();
    //Serial.println(directionsData);
    commandIndex=0;
    command="";
     //Serial.print("@");
     //Serial.print( directionsData.indexOf("@") );
     //Serial.print("#");
     //Serial.println( directionsData.indexOf("#") );
     //Serial.println( directionsData.substring( directionsData.indexOf("#")+1,directionsData.length() ) );
   while( ( directionsData.indexOf("@")>=0 && directionsData.indexOf("#")>=0 ) && (directionsData.indexOf("@")<directionsData.indexOf("#")) && (directionsData.length()>0) )
   {
     command=directionsData.substring( directionsData.indexOf("@")+1,directionsData.indexOf("#") );
     directionsData=directionsData.substring( directionsData.indexOf("#")+1,directionsData.length() );
     
     Serial.println(command);
     stack.enqueue(command);
     commandIndex++;
   }
   int apoklisi=3;
   while (!stack.isEmpty ())
   {
       directionsData=stack.dequeue();
      if(directionsData.startsWith("w")){
        Serial.println("Forward ");
        
       //forward speed assign
        if(directionsData.length() > 1){
          Serial.println("motorspeed");
        //essential little fix because motors differentiate on speed
        motorspeedRight = directionsData.substring(1,4).toInt();
        motorspeedLeft = directionsData.substring(5,8).toInt()-apoklisi;
        }
          Serial.print("l");
          Serial.print(motorspeedRight);
          Serial.print("r");
          Serial.println(motorspeedLeft);
       
        moveForward(myMotor, myMotor2);
      } 
      else if (directionsData.startsWith("s")){
        Serial.println("Backward ");
        
       //backward speed assign
        if(directionsData.length() > 1){
          Serial.println("motorspeed");
        motorspeedRight = directionsData.substring(1,4).toInt();
        motorspeedLeft = directionsData.substring(5,8).toInt() - apoklisi;
        }
          Serial.print("l");
          Serial.print(motorspeedRight);
          Serial.print("r");
          Serial.println(motorspeedLeft);
       
        moveBackward(myMotor, myMotor2);
      } 
      else if (directionsData.startsWith("a")){
        Serial.println("Left");
       
       //left speed assign
        if(directionsData.length() > 1){
          Serial.println("motorspeed");
        motorspeedRight = directionsData.substring(1,4).toInt();
        motorspeedLeft = directionsData.substring(5,8).toInt() - apoklisi;
        }
          Serial.print("l");
          Serial.print(motorspeedRight);
          Serial.print("r");
          Serial.println(motorspeedLeft);
        
        moveLeft(myMotor, myMotor2);
      } 
      else if (directionsData.startsWith("d")){
        Serial.println("Right");
        
       //right speed assign
        if(directionsData.length() > 1){
          Serial.println("motorspeed");
        motorspeedRight = directionsData.substring(1,4).toInt();
        motorspeedLeft = directionsData.substring(5,8).toInt() - apoklisi;
        }
          Serial.print("l");
          Serial.print(motorspeedRight);
          Serial.print("r");
          Serial.println(motorspeedLeft);
             
        moveRight(myMotor, myMotor2);
      }
  
      //break!!
      else if(directionsData.startsWith("q")){
        Serial.println("Stop");
        myMotor->run(RELEASE);
        myMotor2->run(RELEASE);
      }
    }
  }
}
//moving functions
void moveForward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  motor->run(FORWARD);
  motor2->run(FORWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveBackward(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  motor->run(BACKWARD);
  motor2->run(BACKWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveRight(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  motor2->run(BACKWARD);
  motor->run(FORWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}
void moveLeft(Adafruit_DCMotor *motor, Adafruit_DCMotor *motor2){
  motor2->run(FORWARD);
  motor->run(BACKWARD);
  motor->setSpeed(motorspeedLeft);
  motor2->setSpeed(motorspeedRight);
}