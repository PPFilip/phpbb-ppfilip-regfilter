# Filter registrations by country
This phpBB extension is a mashup and heavily copies following 2 extensions:
- "Filter by country" by Mark D. Hamill, https://www.phpbbservices.com
- "Akismet" by Jakub Senko, https://github.com/senky/phpbb-ext-akismet

Combined together, this allows to query MaxMind's GeoLite or GeoIP database, and put users form unwanted countries into 
specific group. This group should have posting and/or messaging privileges removed by setting them to NONE in phpbb group
settings.

## Data source
- This product includes GeoLite2 data created by MaxMind, available from https://www.maxmind.com

## Design choices
- The idea was to develop this as rapidly as possible, therefore only few parts of code are customized, sice above 
mentioned plugins provided great boilerplate for this.
- There is no option to disable registrations. For now, this is by design. Putting users to separate group avoids 
problems with false positives
- No offline database is saved. Since registration volume is low, it is better to just query each successfull 
registration against MaxMind's web API
- No library is used to query the API, curl requests are fine for this and do not add any dependency

## Installation
- just checkout the code into phpBB/ext/ppfilip/regfilter . For now, this is not included in an official phpBB 
database.
- Enable extension in phpBB admin panel
- Create group for spammers and customize posting privileges to your liking. (Note: you may want to add board 
announcement only visible by this group, so false positives can contact admin)
- Get MaxMind API id and key
- Enable extension
- Enjoy

## Suggestions
- Feel free to open tickets or pull requests

