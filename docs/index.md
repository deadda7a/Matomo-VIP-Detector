# VipDetector

## Quick start

1. Install the plugin
2. Create your Json file
   1. If you don't have one, you can use the one I [created](http://austroedit-ranges.sebastian-elisa-pfeifer.eu/) with Austrian Government Agencies
3. Import it ```./console vipdetector:import-data /path/to/file.json```

### Json File Structure

```json
[
 {
  "name": "Example Org 1",
  "ranges": [
   "192.0.2.0/24",
   "198.51.100.0/24"
  ]
 },
 {
  "name": "Example Org 2",
  "ranges": [
   "203.0.113.0/24",
   "2001:db8::/32"
  ]
 }
]
```
