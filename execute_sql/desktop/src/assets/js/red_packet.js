
/**
 * 红包实体
 * @constructor
 * @param {number} point - 红包点数
 * @param {number} position - 红包所在格子
 */
function RedPacket(point, position) {
    this.point = point;
    this.position = position;
}
RedPacket.prototype.toString = function () { return this.position + "";}
