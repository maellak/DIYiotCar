/*****************************************************************
LSM9DS0_Simple.ino
SFE_LSM9DS0 Library Simple Example Code
Jim Lindblom @ SparkFun Electronics
Original Creation Date: February 18, 2014
https://github.com/sparkfun/LSM9DS0_Breakout

The LSM9DS0 is a versatile 9DOF sensor. It has a built-in
accelerometer, gyroscope, and magnetometer. Very cool! Plus it
functions over either SPI or I2C.

This Arduino sketch is a demo of the simple side of the
SFE_LSM9DS0 library. It'll demo the following:
* How to create a LSM9DS0 object, using a constructor (global
  variables section).
* How to use the begin() function of the LSM9DS0 class.
* How to read the gyroscope, accelerometer, and magnetometer
  using the readGryo(), readAccel(), readMag() functions and the
  gx, gy, gz, ax, ay, az, mx, my, and mz variables.
* How to calculate actual acceleration, rotation speed, magnetic
  field strength using the calcAccel(), calcGyro() and calcMag()
  functions.
* How to use the data from the LSM9DS0 to calculate orientation
  and heading.

Hardware setup: This library supports communicating with the
LSM9DS0 over either I2C or SPI. If you're using I2C, these are
the only connections that need to be made:
	LSM9DS0 --------- Arduino
	 SCL ---------- SCL (A5 on older 'Duinos')
	 SDA ---------- SDA (A4 on older 'Duinos')
	 VDD ------------- 3.3V
	 GND ------------- GND
(CSG, CSXM, SDOG, and SDOXM should all be pulled high jumpers on 
  the breakout board will do this for you.)
  
If you're using SPI, here is an example hardware setup:
	LSM9DS0 --------- Arduino
          CSG -------------- 9
          CSXM ------------- 10
          SDOG ------------- 12
          SDOXM ------------ 12 (tied to SDOG)
          SCL -------------- 13
          SDA -------------- 11
          VDD -------------- 3.3V
          GND -------------- GND
	
The LSM9DS0 has a maximum voltage of 3.6V. Make sure you power it
off the 3.3V rail! And either use level shifters between SCL
and SDA or just use a 3.3V Arduino Pro.	  

Development environment specifics:
	IDE: Arduino 1.0.5
	Hardware Platform: Arduino Pro 3.3V/8MHz
	LSM9DS0 Breakout Version: 1.0

This code is beerware. If you see me (or any other SparkFun 
employee) at the local, and you've found our code helpful, please 
buy us a round!

Distributed as-is; no warranty is given.
*****************************************************************/

// The SFE_LSM9DS0 requires both the SPI and Wire libraries.
// Unfortunately, you'll need to include both in the Arduino
// sketch, before including the SFE_LSM9DS0 library.
#include <SPI.h> // Included for SFE_LSM9DS0 library
#include <Wire.h>
#include <SFE_LSM9DS0.h>

///////////////////////
// Example I2C Setup //
///////////////////////
// Comment out this section if you're using SPI
// SDO_XM and SDO_G are both grounded, so our addresses are:
#define LSM9DS0_XM  0x1D // Would be 0x1E if SDO_XM is LOW
#define LSM9DS0_G   0x6B // Would be 0x6A if SDO_G is LOW
// Create an instance of the LSM9DS0 library called `dof` the
// parameters for this constructor are:
// [SPI or I2C Mode declaration],[gyro I2C address],[xm I2C add.]
LSM9DS0 dof(MODE_I2C, LSM9DS0_G, LSM9DS0_XM);

///////////////////////
// Example SPI Setup //
///////////////////////
/* // Uncomment this section if you're using SPI
#define LSM9DS0_CSG  9  // CSG connected to Arduino pin 9
#define LSM9DS0_CSXM 10 // CSXM connected to Arduino pin 10
LSM9DS0 dof(MODE_SPI, LSM9DS0_CSG, LSM9DS0_CSXM);
*/

// Do you want to print calculated values or raw ADC ticks read
// from the sensor? Comment out ONE of the two #defines below
// to pick:

#define PRINT_CALCULATED
//#define PRINT_RAW
//int command;
float pitch, roll, rolloffset, pitchoffset, pitchfix, rollfix;
byte flag = 0;
int i;
#define PRINT_SPEED 500 // 500 ms between prints


void setup() //-------------------------------------------------------------------------------------------------------------------------------------------
{
  Serial.begin(9600); // Start serial at 115200 bps
  // Use the begin() function to initialize the LSM9DS0 library.
  // You can either call it with no parameters (the easy way):
  uint16_t status = dof.begin();
  
  // Or call it with declarations for sensor scales and data rates:  
  //uint16_t status = dof.begin(dof.G_SCALE_2000DPS, 
  //                            dof.A_SCALE_6G, dof.M_SCALE_2GS);
  
  // begin() returns a 16-bit value which includes both the gyro 
  // and accelerometers WHO_AM_I response. You can check this to
  // make sure communication was successful.
  //Serial.print("LSM9DS0 WHO_AM_I's returned: 0x");
  //Serial.println(status, HEX);
  //Serial.println("Should be 0x49D4");
  //Serial.println();

  pitchoffset = 0;
  rolloffset = 0;
  delay(50);
  printOrientation(dof.calcAccel(dof.ax), dof.calcAccel(dof.ay), dof.calcAccel(dof.az));
  delay(100);
  pitchoffset = pitch;
  rolloffset = roll;
  delay(50);
}

void loop()    //-------------------------------------------------------------------------------------------------------------------------------------------
{
  Serial.print("@");
  printAccel(); // Print "A: ax, ay, az"
  Serial.print("*");
  printGyro();  // Print "G: gx, gy, gz"
  Serial.print("*");
  printMag();   // Print "M: mx, my, mz"
Serial.print("*");
   if (flag==0)
  {  
   float axcf = 0;
   float aycf = 0;
   float azcf = 0;

   for (i=0; i<200; i++) 
  {
    dof.readAccel(); 
     float ax = (dof.calcAccel(dof.ax));
    axcf += ax;
     float ay = (dof.calcAccel(dof.ay));
    aycf += ay;
     float az = (dof.calcAccel(dof.az));
    azcf += az; 
    delay(1);
  } 
  
  axcf = axcf/i;
  aycf = aycf/i;
  azcf = azcf/i;
  
  //printOrientation(axcf, aycf, azcf);
  pitch = atan2(axcf, sqrt(aycf * aycf) + (azcf * azcf));
  roll = atan2(aycf, sqrt(axcf * axcf) + (azcf * azcf));
  pitch *= (180.0 / PI);
  roll *= (180.0 / PI);
  
  pitchoffset = pitch;
  rolloffset = roll;
   
  flag = 1;
  } 


   if (Serial.available() > 0) 
        {
                int command = Serial.read();
                switch (command) {
                       case 'z':    
                        //pitchoffset = pitch;
                        //rolloffset = roll;
                        flag = 0;
                        break;
                      case 'r':    
                        pitchoffset = 0;
                        rolloffset = 0;
                        //flag = 0;
                        break;
        }

        
  // Print the heading and orientation for fun!
  //printHeading((float) dof.mx, (float) dof.my);
  // Compasscalc(roll, pitch, dof.mx, dof.my, dof.mz );
  
  printHeading_1(dof.calcMag(dof.mx),dof.calcMag(dof.my),dof.calcMag(dof.mz), pitch, roll);

  printOrientation(dof.calcAccel(dof.ax), dof.calcAccel(dof.ay), dof.calcAccel(dof.az));
  Serial.println("#");
  
  delay(PRINT_SPEED);
}

void printGyro()       //-------------------------------------------------------------------------------------------------------------------------------------------
{
  // To read from the gyroscope, you must first call the
  // readGyro() function. When this exits, it'll update the
  // gx, gy, and gz variables with the most current data.
  dof.readGyro();
  
  // Now we can use the gx, gy, and gz variables as we please.
  // Either print them as raw ADC values, or calculated in DPS.
  //Serial.print("G: ");
#ifdef PRINT_CALCULATED
  // If you want to print calculated values, you can use the
  // calcGyro helper function to convert a raw ADC value to
  // DPS. Give the function the value that you want to convert.
  Serial.print(dof.calcGyro(dof.gx), 2);
  Serial.print("*");
  Serial.print(dof.calcGyro(dof.gy), 2);
  Serial.print("*");
  Serial.print(dof.calcGyro(dof.gz), 2);
#elif defined PRINT_RAW
  Serial.print(dof.gx);
  Serial.print("*");
  Serial.print(dof.gy);
  Serial.print("*");
  Serial.print(dof.gz);
#endif
}

void printAccel()     //-------------------------------------------------------------------------------------------------------------------------------------------
{
  // To read from the accelerometer, you must first call the
  // readAccel() function. When this exits, it'll update the
  // ax, ay, and az variables with the most current data.
  dof.readAccel();
  
  // Now we can use the ax, ay, and az variables as we please.
  // Either print them as raw ADC values, or calculated in g's.
  //Serial.print("A: ");
#ifdef PRINT_CALCULATED
  // If you want to print calculated values, you can use the
  // calcAccel helper function to convert a raw ADC value to
  // g's. Give the function the value that you want to convert.
  Serial.print(dof.calcAccel(dof.ax)*9.81, 2);
  Serial.print("*");
  Serial.print(dof.calcAccel(dof.ay)*9.81, 2);
  Serial.print("*");
  Serial.print(dof.calcAccel(dof.az)*9.81, 2);
#elif defined PRINT_RAW 
  Serial.print(dof.ax);
  Serial.print("*");
  Serial.print(dof.ay);
  Serial.print("*");
  Serial.print(dof.az);
#endif

}

void printMag()      //-------------------------------------------------------------------------------------------------------------------------------------------
{
  // To read from the magnetometer, you must first call the
  // readMag() function. When this exits, it'll update the
  // mx, my, and mz variables with the most current data.
  dof.readMag();
  
  // Now we can use the mx, my, and mz variables as we please.
  // Either print them as raw ADC values, or calculated in Gauss.
  //Serial.print("M: ");
#ifdef PRINT_CALCULATED
  // If you want to print calculated values, you can use the
  // calcMag helper function to convert a raw ADC value to
  // Gauss. Give the function the value that you want to convert.
  Serial.print(dof.calcMag(dof.mx), 2);
  Serial.print("*");
  Serial.print(dof.calcMag(dof.my), 2);
  Serial.print("*");
  Serial.print(dof.calcMag(dof.mz), 2);
#elif defined PRINT_RAW
  Serial.print(dof.mx);
  Serial.print("*");
  Serial.print(dof.my);
  Serial.print("*");
  Serial.print(dof.mz);
#endif
}

// Here's a fun function to calculate your heading, using Earth's
// magnetic field.
// It only works if the sensor is flat (z-axis normal to Earth).
// Additionally, you may need to add or subtract a declination
// angle to get the heading normalized to your location.
// See: http://www.ngdc.noaa.gov/geomag/declination.shtml
void printHeading(float hx, float hy)   //--------------------------------------------------------------------------------------------------------------------------------------
{
  float heading;
  
  if (hy > 0)
  {
    heading = 90 - (atan(hx / hy) * (180 / PI));
  }
  else if (hy < 0)
  {
    heading = 270 - (atan(hx / hy) * (180 / PI));
  }
  else // hy = 0
  {
    if (hx < 0) heading = 180;
    else heading = 0;
  }
  
  //Serial.print("Heading: ");
  Serial.print("*");
  Serial.print(heading, 2);
}

// Another fun function that does calculations based on the
// acclerometer data. This function will print your LSM9DS0's
// orientation -- it's roll and pitch angles.
void printOrientation(float x, float y, float z)       //--------------------------------------------------------------------------------------------------------------
{
  
  pitch = atan2(x, sqrt(y * y) + (z * z));
  roll = atan2(y, sqrt(x * x) + (z * z));
  pitch *= (180.0 / PI);
  roll *= (180.0 / PI);

  
  pitchfix = pitch - pitchoffset;
  rollfix = roll- rolloffset; 
  
  
  //Serial.print("Pitch, Roll: ");
  Serial.print("*");
  Serial.print(pitchfix, 2);
  Serial.print("*");
  Serial.print(rollfix, 2);
}

//--------------------------------------------------------------------------------------------------------------------------------
/*
void Compasscalc(float roll, float pitch,float mag_x, float mag_y, float mag_z )
{
    float headX;
    float headY;
    float cos_roll;
    float sin_roll;
    float cos_pitch;
    float sin_pitch;

    cos_roll = cos(roll);  // Optimizacion, se puede sacar esto de la matriz DCM?
    sin_roll = 1  - (cos_roll * cos_roll);
    cos_pitch = cos(pitch);
    sin_pitch = 1  - (cos_pitch * cos_pitch);

    // Tilt compensated magnetic field X component:
    headX = mag_x*cos_pitch+mag_y*sin_roll*sin_pitch+mag_z*cos_roll*sin_pitch;
    // Tilt compensated magnetic field Y component:
    headY = mag_y*cos_roll-mag_z*sin_roll;
    // magnetic heading
    float heading = atan2(-headY,headX);
    float _declination;
    float heading_x;
    float heading_y;

    _declination = 4.13;
    // Declination correction (if supplied)
    if( fabs(_declination) > 0.0 )
    {
        heading = heading + _declination;
        if (heading > M_PI)    // Angle normalization (-180 deg, 180 deg)
            heading -= (2.0 * M_PI);
        else if (heading < -M_PI)
            heading += (2.0 * M_PI);
    }

    heading_x = cos(heading);
    heading_y = sin(heading);
    printHeading(heading_x,heading_y);
}
*/


//--------------------------------------------------------------------------------------------------------------------------------

//*
 void printHeading_1(float hx, float hy, float hz, float rol, float pitc)
{
 
  float heading;
  float x;
  float y;
  float coss_roll;
  float sn_roll;
  float coss_pitch;
  float sn_pitch;
 
  coss_roll = cos(rol);
  sn_roll = 1  - (coss_roll * coss_roll);
   coss_pitch = cos(pitc);
  sn_pitch = 1  - (coss_pitch * coss_pitch);
 
  x = hx*coss_pitch+hy*sn_roll*sn_pitch+hz*coss_roll*sn_pitch;
  y = hy*coss_roll-hz*sn_roll;
 
  heading = atan2(-y,x);
  
  if (hy > 0)
  {
    heading = 90 - (atan(hx / hy) * (180 / PI));
  }
  else if (hy < 0)
  {
    heading = 270 - (atan(hx / hy) * (180 / PI));
  }
  else // hy = 0
  {
  if (hx < 0) heading = 180;
  else heading = 0;
  }
  
  //char buffh[10];
  //String HeadString = "";
 
  //dtostrf(heading, 5, 2, buffh);  //5 is mininum width, 2 is precision
  //HeadString += buffh;
 
  //Serial.println(HeadString);

  Serial.print(heading);
}
 //*/

