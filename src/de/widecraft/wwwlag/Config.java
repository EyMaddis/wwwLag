package de.widecraft.wwwlag;

import org.bukkit.configuration.file.FileConfiguration;
import org.bukkit.plugin.Plugin;

public class Config {
	Plugin plugin;
	private FileConfiguration config;

	public Config(wwwlag wwwlag) {
		this.plugin = wwwlag;
	}

	public void load() {
		plugin.reloadConfig();
		config = plugin.getConfig();
		
		config.addDefault("url", "http://www.widecraft.de/wwwlag.php");
		config.addDefault("token", "widecraft.de");
		config.addDefault("interval", 10);
		config.addDefault("debug", "true");
		
		config.options().copyDefaults(true);
		plugin.saveConfig();
	}

	public String get_value(String key) {
		return config.getString(key);
	}
	
	public int int_get_value(String key) {
		return config.getInt(key);
	}

}
