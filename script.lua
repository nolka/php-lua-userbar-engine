package.path = package.path .. ";./lualib/?.lua"

require"Drawable"
require"ProgressBar"
require"User"

function frame1()

    setstrokeantialias(true)
    line(0, 0, 50, 450)

    pb = ProgressBar:new(0, 0, 319, 149)
    pb:setValue(50);

    local graphics = list_files()
    for i = 1, #graphics do
        draw_image(graphics[math.random(#graphics)], math.random(300), math.random(130))
    end

    xc, yc = 150, 150
    dc, dy = 200, 200
    points = {}
    for i = -1500, 1500 do
        x = xc + i * 5
        y = (math.sin(i) * 15) + math.cos(yc) + yc
        table.insert(points, { x = x, y = y })
    end
    
    pb = ProgressBar:new(54, 70, 200, 10)
    pb.borderColor = "green"
    pb.progressColor = "red"
    pb:setProgress(40)

end

