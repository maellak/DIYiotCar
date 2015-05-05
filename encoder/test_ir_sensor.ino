int sensorPin = 0; //analog pin 0
int aktina=0;
int prevActina=0;
int aktines=5;
int strofes=0;
int averageDistance=500;


void setup(){
  Serial.begin(9600);
}

void loop(){
  int val = analogRead(sensorPin);
  if ( val<averageDistance && prevActina>averageDistance ) 
  {aktina++;Serial.print(aktina);Serial.print(" ");}
  if ( aktines==aktina ) 
  {
    aktina=0;
    strofes++;
    Serial.print("|");
    Serial.println(strofes);
  }
  //Serial.println(val);
  prevActina=val;

  //just to slow down the output - remove if trying to catch an object passing by
  delay(5);

}
