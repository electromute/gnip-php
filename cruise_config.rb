# this is the file that cruise control uses to configure its own cruise build at 
# http://cruisecontrolrb.thoughtworks.com/
#   simple, ain't it

Project.configure do |project|
  project.email_notifier.emails = ["jeremystellsmith@gmail.com", "wes@pivotallabs.com"]
  project.build_command = "phing"
end
