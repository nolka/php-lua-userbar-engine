Drawable = {}
_G.classes = {}
_G.classes.drawableCount = -1

function Drawable:new(x, y, w, h, drawableName)
    if x == nil then x = 0 end
    if y == nil then y = 0 end
    if w == nil then w = 0 end
    if h == nil then h = 0 end

    _G.classes.drawableCount = _G.classes.drawableCount+1
    newDrawable = {
        name = drawableName,
        location = {
            x = x,
            y = y
        },
        size = {
            width = w,
            height = h
        }
    }
    if newDrawable.name == nil then
        newDrawable.name = "unnamed".._G.classes.drawableCount
    end
    self.__index = self
    return setmetatable(newDrawable, self)
end


function Drawable:draw()
end
