package de.widecraft.wwwlag;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.util.logging.Logger;

import org.bukkit.plugin.PluginDescriptionFile;
import org.bukkit.plugin.java.JavaPlugin;

public class wwwlag extends JavaPlugin{
	
	public final Logger log = Logger.getLogger("Minecraft");
	private String version;
	public Config config = new Config(this);
	private TpsMonitor tpsMeter;

	public void onEnable() {	
		PluginDescriptionFile pdfFile = this.getDescription();
		this.version = pdfFile.getVersion();
		this.tpsMeter = new TpsMonitor(this);
		getServer().getScheduler().scheduleSyncRepeatingTask(this, this.tpsMeter, 0L, 40L);
		
		this.log.info("[wwwLag v"+this.version+"] Plugin enabling...");
		config.load();
		
		try {
		    Metrics metrics = new Metrics(this);
		    metrics.start();
		} catch (IOException e) {
		    // Failed to submit the stats :-(
		}
		
		this.getServer().getScheduler().runTaskTimerAsynchronously(this, new Runnable() {
		    @Override  
		    public void run() {
		    	Runtime runtime = Runtime.getRuntime();
		    	String tps = Float.toString(tpsMeter.getAverageTps());
		    	String freemem = String.valueOf(runtime.freeMemory() / 1024 / 1024);
		        call_url("tps="+tps+"&memory="+freemem+"&players="+ String.valueOf(getServer().getOnlinePlayers().length));
		    }
		}, 1200L, this.config.int_get_value("interval")*20*60);
	}
	
public String call_url(String call) {
		
		String result = "";	
		call = this.config.get_value("url") + "?token="+this.config.get_value("token") + "&" + call;

		try {
		    // Create a URL for the desired page
		    URL url = new URL(call);

		    // Read all the text returned by the server
		    BufferedReader in = new BufferedReader(new InputStreamReader(url.openStream()));
		    String str;
		    while ((str = in.readLine()) != null) {
		        result = str;
		    }
		    in.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
		this.log.info("[wwwLog] " + result);
		return result;
	}
}
