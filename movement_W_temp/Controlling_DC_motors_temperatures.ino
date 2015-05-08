#include <OneWire.h>


#include <Wire.h>
#include <Adafruit_MotorShield.h>
#include "utility/Adafruit_PWMServoDriver.h"
//#include <Adafruit_PWMServoDriver.h>
// Setting up Motors on Adafruit Motorshield
// Left Motor is Motor 4 and Right Motor is Motor 3
Adafruit_MotorShield AFMS = Adafruit_MotorShield();
Adafruit_DCMotor *myMotor = AFMS.getMotor(3);
Adafruit_DCMotor *myMotor2 = AFMS.getMotor(4);
OneWire  ds(10);
#define MOTORSPEED 255 //255 is maximum speed
void setup() {
  Serial.begin(9600); // set up Serial library at 9600 bps
  
  AFMS.begin();
  myMotor->setSpeed(150);
  myMotor->run(FORWARD);
  // turn on motor
  myMotor->run(RELEASE);
  myMotor2->setSpeed(150);
  myMotor2->run(FORWARD);
  // turn on motor
  myMotor2->run(RELEASE);
}
void loop() {
  getTemp();
  
  uint8_t i;
  if(Serial.available() > 0){
    char test = Serial.read();
    Serial.println();
    //Serial.println(test);
    if(test == 'w'){
      Serial.println("Forward ");
      moveForward(myMotor, myMotor2);
    } 
    else if (test == 's'){
      Serial.println("Backward ");
      moveBackward(myMotor, myMotor2);
    } 
    else if (test == 'a'){
      Serial.println("Left");
      moveLeft(myMotor, myMotor2);
    } 
    else if (test == 'd'){
      Serial.println("Right");
      moveRight(myMotor, myMotor2);
    }
    else if(test == ' '){
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
void getTemp() {
  byte i;
  byte present = 0;
  byte type_s;
  byte data[12];
  byte addr[8];
  float celsius;
  
  if ( !ds.search(addr)) {
    //Serial.println("No more addresses.");
    //Serial.println();
    ds.reset_search();
    delay(250);
    return;
  }
  
 /* Serial.print("ROM =");
  for( i = 0; i < 8; i++) {
    Serial.write(' ');
    Serial.print(addr[i], HEX);
  }*/

  if (OneWire::crc8(addr, 7) != addr[7]) {
      Serial.println("CRC is not valid!");
      return;
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
      Serial.println("Device is not a DS18x20 family device.");
      return;
  } 

  ds.reset();
  ds.select(addr);
  ds.write(0x44, 1);        // start conversion, with parasite power on at the end
  
  delay(1000);     // maybe 750ms is enough, maybe not
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
  Serial.print("Temperature is :");
  Serial.print('@');
  Serial.print(celsius);
  Serial.println('#');
  //Serial.print(" Celsius");
  //Serial.print(fahrenheit);
  //Serial.println(" Fahrenheit");
}

