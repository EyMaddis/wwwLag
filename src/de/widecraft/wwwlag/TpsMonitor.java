package de.widecraft.wwwlag;

import java.util.LinkedList;

public class TpsMonitor
implements Runnable
{
private final LinkedList<Float> loggedTps = new LinkedList<Float>();
private long lastCall = getMillis() - 3000L;
private float tps = 20.0F;
private int interval = 40;

public TpsMonitor(wwwlag plugin)
{
}

public void addTps(Float tps)
{
  if ((tps != null) && (tps.floatValue() <= 20.0F)) {
    this.loggedTps.add(tps);
  }
  if (this.loggedTps.size() > 10)
    this.loggedTps.poll();
}

public final float getAverageTps()
{
  float amount = 0.0F;
  for (Float f : this.loggedTps) {
    if (f != null) {
      amount += f.floatValue();
    }
  }
  return amount / this.loggedTps.size();
}

private long getMillis()
{
  return System.currentTimeMillis();
}

public final float getTps()
{
  return this.tps;
}

public final void run()
{
  long currentTime = getMillis();
  long spentTime = (currentTime - this.lastCall) / 1000L;
  if (spentTime == 0L) {
    spentTime = 1L;
  }

  float calculatedTps = (float)(this.interval / spentTime);
  if (calculatedTps > 20.0F) {
    calculatedTps = 20.0F;
  }

  setTps(calculatedTps);
  addTps(Float.valueOf(calculatedTps));
  this.lastCall = getMillis();
}

private void setTps(float newTps)
{
  this.tps = newTps;
}
}