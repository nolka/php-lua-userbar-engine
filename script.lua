package.path = package.path..";./lib/?.lua"

require "ProgressBar"

function main(data)
    annotation(10, 200, #data.channel.title)
    for i=1,#data.channel.item do
      annotation(10, 200+13*i, data.channel.item[i].title)
    
    end
    setstrokeantialias(true)
    line(0,0,50, 450)
    
    pb = ProgressBar:new(50, 50, 150, 10)
    pb:setProgress(55)

--[[
for x:=0 to 320 do
  PutPixel(x, Round(sin(x / 50 * pi) * 50) + 100);
    
    ]]

    xc, yc = 150, 150
    dc, dy = 100, 100
    points = {}
    for i=-1500,1500 do
      x = xc+i*2
      y = (math.sin(i)*10)+math.cos(yc)+yc
       table.insert(points, {x=x, y=y})
    end

    for i,v in ipairs(points) do
      s, d = v, points[i+1]
      if d ~= nil then
	line(s.x, s.y, d.x, d.y)
      end
    end

end