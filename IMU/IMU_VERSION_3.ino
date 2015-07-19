#include <SPI.h> // 
#include <Wire.h>
#include <SFE_LSM9DS0.h>

///////////////////////
// Example I2C Setup //
///////////////////////

// SDO_XM and SDO_G are both grounded, so our addresses are:

#define LSM9DS0_XM  0x1D // Would be 0x1E if SDO_XM is LOW
#define LSM9DS0_G   0x6B // Would be 0x6A if SDO_G is LOW

// Create an instance of the LSM9DS0 library called `dof` the
// parameters for this constructor are:
// [SPI or I2C Mode declaration],[gyro I2C address],[xm I2C add.]

LSM9DS0 dof(MODE_I2C, LSM9DS0_G, LSM9DS0_XM);

#define PRINT_CALCULATED

// #define PRINT_RAW //

#define PRINT_SPEED 500 // 1000 ms between prints
       
#define Kp 2.0f * 5.0f // these are the free parameters in the Mahony filter and fusion scheme, Kp for proportional feedback, Ki for integral
#define Ki 0.0f


float deltat = 0.0f;        // integration interval for both filter schemes

float q[4] = {1.0f, 0.0f, 0.0f, 0.0f};   // vector to hold quaternion
float eInt[3] = {0.0f, 0.0f, 0.0f};       // vector to hold integral error for Mahony method

float ax, ay, az;
float axcf, aycf, azcf;
float gx, gy, gz;
float mx, my, mz;

int i;
byte flag = 0;

float R_y;
float R_x;


void setup()
{
  Serial.begin(9600); // Start serial at 9600 bps 
  //uint16_t status = dof.begin();
  //uint16_t status = dof.begin(dof.G_SCALE_245DPS, 
                            // dof.A_SCALE_2G, dof.M_SCALE_2GS);
                            
                            
                            uint16_t status =  dof.begin(dof.G_SCALE_245DPS, dof.A_SCALE_2G, dof.M_SCALE_2GS,dof.G_ODR_95_BW_125, dof.A_ODR_50, dof.M_ODR_3125);
                            
 dof.setAccelABW(dof.A_ABW_773);  
}

void loop()
{ 
  // Accelometer Calibration
  if (flag==0)
  {  
   axcf = 0;
   aycf = 0;
   azcf = 0;
 
  for (i=0; i<100; i++) 
  {
    dof.readAccel(); 
    ax = (dof.calcAccel(dof.ax));
    axcf += ax;
    
    ay = (dof.calcAccel(dof.ay));
    aycf += ay;
    
    az = (dof.calcAccel(dof.az));
    azcf += az; 
     
    
  } 
  axcf = axcf/i;
  aycf = aycf/i;
  azcf = azcf/i;
  //Serial.print("Final axcf=");
  //Serial.println(axcf);
  //Serial.println(aycf);
  //Serial.println(azcf);
   
  
   
  flag = 1;
  } 
  
  MahonyQuaternionUpdate( ax,  ay,  az,  gx,  gy,  gz,  mx,  my,  mz);
  
  R_y = a_ngl_e (ax,ay,az,0);
  R_x = a_ngl_e (ax,ay,az,1);  
  
  Serial.print('@');
  printAccel();           // Print "A: ax, ay, az"
  Serial.print('*');
  printGyro();            // Print "G: gx, gy, gz"
  Serial.print('*');
  calcuMag();             // Calculate "M: mx, my, mz"
  
  //printHeading(mx, my, mz);   // Print Heading - works only in if the sensor is flat (z-axis normal to Earth).
  
  
  printHeading_2(mx, my, mz, R_y, R_x);
  
  Serial.print('*');
 





  
  
  


  //Serial.print("Degrees Y - AXIS: ");
  Serial.print(R_y);
  Serial.print('*');
  Serial.print(R_x);
  Serial.println('#');
  //Serial.println();

  
  
  //Serial.print("Degrees X - AXIS: ");
  //Serial.print(R_x);
  //Serial.println();
  //Serial.println(); 

  
  delay(PRINT_SPEED);
}
              
//--------------------------------------------
void printAccel()
{
  dof.readAccel();
  ax=(dof.calcAccel(dof.ax));
  ay=(dof.calcAccel(dof.ay));
  az=(dof.calcAccel(dof.az));

  ax -= axcf;
  ay -= aycf;
  az = az +(1 -azcf);
  
  /*if (ax >= 0)
  ax -= axcf;
  else
  ax += axcf;
   
  if (ay >= 0)
  ay -= aycf;
  else
  ay += aycf;
    
  if (az >= 0)
  az -= azcf;
  else
  az += azcf;
  */
  
  char buffa[10];
  String AccelString = "";

  dtostrf(ax, 4, 2, buffa);  //5 is mininum width, 2 is precision
  AccelString += buffa;
  AccelString += "*";
  dtostrf(ay, 4, 2, buffa);  //5 is mininum width, 2 is precision
  AccelString += buffa;
  AccelString += "*";
  dtostrf(az, 4, 2, buffa);  //5 is mininum width, 2 is precision
  AccelString += buffa;

  Serial.print(AccelString); 
}
//--------------------------------------------
void printGyro()
{
  dof.readGyro();
  gx=(dof.calcGyro(dof.gx));
  gy=(dof.calcGyro(dof.gy));
  gz=(dof.calcGyro(dof.gz));

  char buffg[10];
  String GyroString = "";
   
  dtostrf(gx, 4, 2, buffg);  //5 is mininum width, 2 is precision
  GyroString += buffg;
  GyroString += "*";
  dtostrf(gy, 4, 2, buffg);  //5 is mininum width, 2 is precision
  GyroString += buffg;
  GyroString += "*";
  dtostrf(gz, 4, 2, buffg);  //5 is mininum width, 2 is precision
  GyroString += buffg;

  Serial.print(GyroString);               //
}
//--------------------------------------------
void calcuMag()
{
  dof.readMag();
  mx=(dof.calcMag(dof.mx)) ;
  my=(dof.calcMag(dof.my)) ;
  mz=(dof.calcMag(dof.mz)) ;
   
  /*char buffm[10];
  String MagString = "";

  dtostrf(mx, 4, 2, buffm);  //5 is mininum width, 2 is precision
  MagString += buffm;
  MagString += "*";
  dtostrf(my, 4, 2, buffm);  //5 is mininum width, 2 is precision
  MagString += buffm;
  MagString += "*";
  dtostrf(mz, 4, 2, buffm);  //5 is mininum width, 2 is precision
  MagString += buffm;

  Serial.print(MagString); */
}
//--------------------------------------------
void printHeading(float hx, float hy, float hz)
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
      
  char buffh[10];
  String HeadString = "";
  
  dtostrf(heading, 5, 2, buffh);  //5 is mininum width, 2 is precision
  HeadString += buffh;
  
  Serial.print(HeadString);
}
//--------------------------------------------


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
  
  x = hx*coss_pitch + hy*sn_roll*sn_pitch + hz*coss_roll*sn_pitch;
  
  y = hy*coss_roll - hz*sn_roll;
  
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
      
  char buffh[10];
  String HeadString = "";
  
  dtostrf(heading, 5, 2, buffh);  //5 is mininum width, 2 is precision
  HeadString += buffh;
  
  Serial.print(HeadString);
}
//--------------------------------------------





void printHeading_2(float hx, float hy, float hz, float rol, float pitc)
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
  
  x = hx*coss_pitch + hy*sn_roll*sn_pitch + hz*coss_roll*sn_pitch;
  
  y = hy*coss_roll - hz*sn_roll;
  
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
      
  if (pitc>0)
  heading += pitc;
  
  if (rol<0)
  heading += rol;
  
  char buffh[10];
  String HeadString = "";
  
  dtostrf(heading, 5, 2, buffh);  //5 is mininum width, 2 is precision
  HeadString += buffh;
  
  Serial.print(HeadString);
}
//--------------------------------------------





void MahonyQuaternionUpdate(float ax, float ay, float az, float gx, float gy, float gz, float mx, float my, float mz)
{
  float q1 = q[0], q2 = q[1], q3 = q[2], q4 = q[3];   // short name local variable for readability
  float norm;
  float hx, hy, bx, bz;
  float vx, vy, vz, wx, wy, wz;
  float ex, ey, ez;
  float pa, pb, pc;

  // Auxiliary variables to avoid repeated arithmetic
  float q1q1 = q1 * q1;
  float q1q2 = q1 * q2;
  float q1q3 = q1 * q3;
  float q1q4 = q1 * q4;
  float q2q2 = q2 * q2;
  float q2q3 = q2 * q3;
  float q2q4 = q2 * q4;
  float q3q3 = q3 * q3;
  float q3q4 = q3 * q4;
  float q4q4 = q4 * q4;   

  // Normalise accelerometer measurement
  norm = sqrt(ax * ax + ay * ay + az * az);
  if (norm == 0.0f) return; // handle NaN
  norm = 1.0f / norm;        // use reciprocal for division
  ax *= norm;
  ay *= norm;
  az *= norm;
  // Normalise magnetometer measurement
  norm = sqrt(mx * mx + my * my + mz * mz);
  if (norm == 0.0f) return; // handle NaN
  norm = 1.0f / norm;        // use reciprocal for division
  mx *= norm;
  my *= norm;
  mz *= norm;
  // Reference direction of Earth's magnetic field
  hx = 2.0f * mx * (0.5f - q3q3 - q4q4) + 2.0f * my * (q2q3 - q1q4) + 2.0f * mz * (q2q4 + q1q3);
  hy = 2.0f * mx * (q2q3 + q1q4) + 2.0f * my * (0.5f - q2q2 - q4q4) + 2.0f * mz * (q3q4 - q1q2);
  bx = sqrt((hx * hx) + (hy * hy));
  bz = 2.0f * mx * (q2q4 - q1q3) + 2.0f * my * (q3q4 + q1q2) + 2.0f * mz * (0.5f - q2q2 - q3q3);
 
  // Estimated direction of gravity and magnetic field
  vx = 2.0f * (q2q4 - q1q3);
  vy = 2.0f * (q1q2 + q3q4);
  vz = q1q1 - q2q2 - q3q3 + q4q4;
  wx = 2.0f * bx * (0.5f - q3q3 - q4q4) + 2.0f * bz * (q2q4 - q1q3);
  wy = 2.0f * bx * (q2q3 - q1q4) + 2.0f * bz * (q1q2 + q3q4);
  wz = 2.0f * bx * (q1q3 + q2q4) + 2.0f * bz * (0.5f - q2q2 - q3q3);  
  // Error is cross product between estimated direction and measured direction of gravity
  ex = (ay * vz - az * vy) + (my * wz - mz * wy);
  ey = (az * vx - ax * vz) + (mz * wx - mx * wz);
  ez = (ax * vy - ay * vx) + (mx * wy - my * wx);
  if (Ki > 0.0f)
  {
    eInt[0] += ex;      // accumulate integral error
    eInt[1] += ey;
    eInt[2] += ez;
  }
  else
  {
    eInt[0] = 0.0f;     // prevent integral wind up
    eInt[1] = 0.0f;
    eInt[2] = 0.0f;
  }
  // Apply feedback terms
  gx = gx + Kp * ex + Ki * eInt[0];
  gy = gy + Kp * ey + Ki * eInt[1];
  gz = gz + Kp * ez + Ki * eInt[2];
  // Integrate rate of change of quaternion
  pa = q2;
  pb = q3;
  pc = q4;
  q1 = q1 + (-q2 * gx - q3 * gy - q4 * gz) * (0.5f * deltat);
  q2 = pa + (q1 * gx + pb * gz - pc * gy) * (0.5f * deltat);
  q3 = pb + (q1 * gy - pa * gz + pc * gx) * (0.5f * deltat);
  q4 = pc + (q1 * gz + pa * gy - pb * gx) * (0.5f * deltat);

  // Normalise quaternion
  norm = sqrt(q1 * q1 + q2 * q2 + q3 * q3 + q4 * q4);
  norm = 1.0f / norm;
  q[0] = q1 * norm;
  q[1] = q2 * norm;
  q[2] = q3 * norm;
  q[3] = q4 * norm;
}
//----------------------------------------------        
float a_ngl_e (float Ax, float Ay, float Az, byte AA)
{  
  float R,Real;
  
  R = sqrt(( Ax*Ax) + (Ay*Ay) + (Az*Az) );

  if (AA == 0)
  { 
    Real = ( (acos(Ay/R)) * (180/PI) ); 
    Real = 90 - Real;
  }
  else 
  {
  Real = ( (acos(Ax/R)) * (180/PI) ); 
  Real = 90 - Real;    
  } 

  
  
  return Real;
} 
  
void printOrientation(float x, float y, float z)
{
  float pitch, roll;
  
  pitch = atan2(x, sqrt(y * y) + (z * z));
  roll = atan2(y, sqrt(x * x) + (z * z));
  pitch *= 180.0 / PI;
  roll *= 180.0 / PI;
  
  Serial.print("Pitch, Roll: ");
  Serial.print(pitch, 2);
  Serial.print(", ");
  Serial.println(roll, 2);
}
