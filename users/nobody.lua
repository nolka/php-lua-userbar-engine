package.path = package.path .. ";./lualib/?.lua"

require"Drawable"
require"ProgressBar"
require"Session"

function declense(value, titles)
    value = math.abs(value)
    local value1 = value % 10
    if value > 10 and value < 20 then return titles[3] end
    if value1 > 1 and value1 < 5 then return titles[2] end
    if value1 == 1 then return titles[1] end
    return titles[3]
end

--[[
--This method called  before draw each frame
]]
function beforeDraw(frame_id)
end

--[[
-- This method called for draw on each frame in frame collection.
-- All draw operation like draw lines, or other geometry must be done here.
-- @var frame_id int current frame number
 ]]
function draw(frame_id)

    -- load_gif("gifka.gif")
    setstrokeantialias(true)
    if Session.get('count') == nil or Session.get('count') >= 100 then
        Session.set('count', 0)
    else
        Session.set('count', Session.get('count') + 16)
    end

    pb = ProgressBar:new(2, 2, 234, 10)
    pb.gauge.margin = 2
    pb.borderColor = "green"
    pb.progressColor = "green"
    pb:setProgress(Session.get('count'))

    create_layer(true)
    pb = ProgressBar:new(2, 23, 200, 6)
    pb.borderColor = "red"
    pb.progressColor = "red"
    pb:setProgress(0)

    --    clone_layer(true)
    --
    --    pb = ProgressBar:new(50, 15, 60, 16)
    --    pb.borderColor = "black"
    --    pb.progressColor = "orange"
    --    pb:setProgress(50)

    for i = 5, 100, 5 do
        create_layer(true)
        pb = ProgressBar:new(50, 15, 60, 16)
        pb.borderColor = "black"
        pb.progressColor = "red"
        pb:setProgress(i)
    end
end

--[[
-- Called after draw each of frames
 ]]
function afterDraw(frame_id)
end

